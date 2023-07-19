<?php declare(strict_types=1);

namespace Shuwei\Core\Framework\Test\DataAbstractionLayer\Search;

use PHPUnit\Framework\TestCase;
use Shuwei\Core\Framework\Context;
use Shuwei\Core\Framework\DataAbstractionLayer\Exception\SearchRequestException;
use Shuwei\Core\Framework\DataAbstractionLayer\Search\ApiCriteriaValidator;
use Shuwei\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shuwei\Core\Framework\DataAbstractionLayer\Search\CriteriaArrayConverter;
use Shuwei\Core\Framework\DataAbstractionLayer\Search\Parser\AggregationParser;
use Shuwei\Core\Framework\DataAbstractionLayer\Search\RequestCriteriaBuilder;
use Shuwei\Core\Framework\DataAbstractionLayer\Search\Sorting\CountSorting;
use Shuwei\Core\Framework\DataAbstractionLayer\Search\Sorting\FieldSorting;
use Shuwei\Core\Framework\Test\TestCaseBase\KernelTestBehaviour;
use Shuwei\Core\System\User\UserDefinition;
use Symfony\Component\HttpFoundation\Request;

/**
 * @internal
 */
class RequestCriteriaBuilderTest extends TestCase
{
    use KernelTestBehaviour;

    /**
     * @var RequestCriteriaBuilder
     */
    private $requestCriteriaBuilder;

    protected function setUp(): void
    {
        parent::setUp();
        $this->requestCriteriaBuilder = $this->getContainer()->get(RequestCriteriaBuilder::class);
    }

    public static function maxApiLimitProvider(): iterable
    {
        yield 'Test null max limit' => [10000, null, 10000, false];
        yield 'Test null max limit and null limit' => [null, null, null, false];
        yield 'Test max limit with null limit' => [null, 100, 100, false];
        yield 'Test max limit with higher limit' => [200, 100, 100, true];
        yield 'Test max limit with lower limit' => [50, 100, 50, false];
    }

    /**
     * @dataProvider maxApiLimitProvider
     */
    public function testMaxApiLimit(?int $limit, ?int $max, ?int $expected, bool $exception = false): void
    {
        $body = ['limit' => $limit];

        $builder = new RequestCriteriaBuilder(
            $this->getContainer()->get(AggregationParser::class),
            $this->getContainer()->get(ApiCriteriaValidator::class),
            $this->getContainer()->get(CriteriaArrayConverter::class),
            $max
        );

        $request = new Request([], $body);
        $request->setMethod(Request::METHOD_POST);

        try {
            $criteria = $builder->handleRequest($request, new Criteria(), $this->getContainer()->get(UserDefinition::class), Context::createDefaultContext());
            static::assertSame($expected, $criteria->getLimit());
        } catch (SearchRequestException) {
            static::assertTrue($exception);
        }

        $request = new Request($body);
        $request->setMethod(Request::METHOD_GET);

        try {
            $criteria = $builder->handleRequest($request, new Criteria(), $this->getContainer()->get(UserDefinition::class), Context::createDefaultContext());
            static::assertSame($expected, $criteria->getLimit());
        } catch (SearchRequestException) {
            static::assertTrue($exception);
        }
    }

    public function testAssociationsAddedToCriteria(): void
    {
        $body = [
            'limit' => 10,
            'page' => 1,
            'associations' => [
                'avatarMedia' => [
                    'limit' => 25,
                    'page' => 1,
                    'filter' => [
                        ['type' => 'equals', 'field' => 'quantityStart', 'value' => 1],
                    ],
                    'sort' => [
                        ['field' => 'quantityStart'],
                    ],
                ],
            ],
        ];

        $request = new Request([], $body, [], [], []);
        $request->setMethod(Request::METHOD_POST);

        $criteria = new Criteria();
        $this->requestCriteriaBuilder->handleRequest(
            $request,
            $criteria,
            $this->getContainer()->get(UserDefinition::class),
            Context::createDefaultContext()
        );

        static::assertTrue($criteria->hasAssociation('avatarMedia'));
        $nested = $criteria->getAssociation('avatarMedia');

        static::assertInstanceOf(Criteria::class, $nested);
        static::assertCount(1, $nested->getFilters());
        static::assertCount(1, $nested->getSorting());
    }

    public function testCriteriaToArray(): void
    {
        $criteria = (new Criteria())
            ->addSorting(new FieldSorting('order.createdAt', FieldSorting::DESCENDING))
            ->addSorting(new CountSorting('transactions.id', CountSorting::ASCENDING))
            ->addAssociation('transactions.paymentMethod')
            ->addAssociation('deliveries.shippingMethod')
            ->setLimit(1)
            ->setOffset((1 - 1) * 1)
            ->setTotalCountMode(100);

        $criteria->getAssociation('transaction')->addSorting(new FieldSorting('createdAt', FieldSorting::DESCENDING));

        $criteriaArray = $this->requestCriteriaBuilder->toArray($criteria);

        $testArray = [
            'total-count-mode' => 100,
            'limit' => 1,
            'associations' => [
                'transactions' => [
                    'total-count-mode' => 0,
                    'associations' => [
                        'paymentMethod' => [
                            'total-count-mode' => 0,
                        ],
                    ],
                ],
                'deliveries' => [
                    'total-count-mode' => 0,
                    'associations' => [
                        'shippingMethod' => [
                            'total-count-mode' => 0,
                        ],
                    ],
                ],
                'transaction' => [
                    'total-count-mode' => 0,
                    'sort' => [
                        [
                            'field' => 'createdAt',
                            'naturalSorting' => false,
                            'extensions' => [],
                            'order' => 'DESC',
                        ],
                    ],
                ],
            ],
            'sort' => [
                [
                    'field' => 'order.createdAt',
                    'naturalSorting' => false,
                    'extensions' => [],
                    'order' => 'DESC',
                ],
                [
                    'field' => 'transactions.id',
                    'naturalSorting' => false,
                    'extensions' => [],
                    'order' => 'ASC',
                    'type' => 'count',
                ],
            ],
        ];

        static::assertEquals($testArray, $criteriaArray);
    }

    public function testManualScoreSorting(): void
    {
        $body = [
            'sort' => [
                [
                    'field' => '_score',
                ],
            ],
        ];
        $request = new Request([], $body, [], [], []);
        $request->setMethod(Request::METHOD_POST);

        $criteria = $this->requestCriteriaBuilder->handleRequest(
            $request,
            new Criteria(),
            $this->getContainer()->get(UserDefinition::class),
            Context::createDefaultContext()
        );

        $sorting = $criteria->getSorting();
        static::assertCount(1, $sorting);
        static::assertEquals('_score', $sorting[0]->getField());
    }

    public function testMaxLimitForAssociations(): void
    {
        $builder = new RequestCriteriaBuilder(
            $this->getContainer()->get(AggregationParser::class),
            $this->getContainer()->get(ApiCriteriaValidator::class),
            $this->getContainer()->get(CriteriaArrayConverter::class),
            100
        );

        $payload = [
            'associations' => [
                'options' => ['limit' => 101],
                'locale' => ['limit' => null],
                'aclRoles' => [],
            ],
        ];

        $criteria = $builder->fromArray($payload, new Criteria(), $this->getContainer()->get(UserDefinition::class), Context::createDefaultContext());

        static::assertTrue($criteria->hasAssociation('options'));
        static::assertTrue($criteria->hasAssociation('aclRoles'));

        static::assertEquals(100, $criteria->getLimit());
        static::assertEquals(101, $criteria->getAssociation('options')->getLimit());
        static::assertNull($criteria->getAssociation('locale')->getLimit());
        static::assertNull($criteria->getAssociation('aclRoles')->getLimit());
    }

    /**
     * @dataProvider providerTotalCount
     */
    public function testDifferentTotalCount(mixed $totalCountMode, int $expectedMode): void
    {
        $payload = [
            'total-count-mode' => $totalCountMode,
        ];

        $criteria = $this->requestCriteriaBuilder->fromArray($payload, new Criteria(), $this->getContainer()->get(UserDefinition::class), Context::createDefaultContext());
        static::assertSame($expectedMode, $criteria->getTotalCountMode());
    }

    public static function providerTotalCount(): iterable
    {
        yield [
            '0',
            Criteria::TOTAL_COUNT_MODE_NONE,
        ];

        yield [
            '1',
            Criteria::TOTAL_COUNT_MODE_EXACT,
        ];

        yield [
            '2',
            Criteria::TOTAL_COUNT_MODE_NEXT_PAGES,
        ];

        yield [
            '3',
            Criteria::TOTAL_COUNT_MODE_NONE,
        ];

        yield [
            '-3',
            Criteria::TOTAL_COUNT_MODE_NONE,
        ];

        yield [
            'none',
            Criteria::TOTAL_COUNT_MODE_NONE,
        ];

        yield [
            'none-2',
            Criteria::TOTAL_COUNT_MODE_NONE,
        ];

        yield [
            'exact',
            Criteria::TOTAL_COUNT_MODE_EXACT,
        ];

        yield [
            'next-pages',
            Criteria::TOTAL_COUNT_MODE_NEXT_PAGES,
        ];
    }
}
