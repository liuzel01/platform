<?php declare(strict_types=1);

namespace Shuwei\Core\Maintenance\Test\SalesChannel\Service;

use PHPUnit\Framework\TestCase;
use Shuwei\Core\Defaults;
use Shuwei\Core\Framework\Context;
use Shuwei\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shuwei\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shuwei\Core\Framework\Log\Package;
use Shuwei\Core\Framework\Test\TestCaseBase\IntegrationTestBehaviour;
use Shuwei\Core\Framework\Uuid\Uuid;
use Shuwei\Core\Maintenance\SalesChannel\Service\SalesChannelCreator;
use Shuwei\Core\System\SalesChannel\SalesChannelEntity;

/**
 * @internal
 */
#[Package('core')]
class SalesChannelCreatorTest extends TestCase
{
    use IntegrationTestBehaviour;

    private SalesChannelCreator $salesChannelCreator;

    private EntityRepository $salesChannelRepository;

    protected function setUp(): void
    {
        $this->salesChannelCreator = $this->getContainer()->get(SalesChannelCreator::class);
        $this->salesChannelRepository = $this->getContainer()->get('sales_channel.repository');
    }

    public function testCreateSalesChannel(): void
    {
        $id = Uuid::randomHex();
        $this->salesChannelCreator->createSalesChannel($id, 'test', Defaults::SALES_CHANNEL_TYPE_API);

        /** @var SalesChannelEntity $salesChannel */
        $salesChannel = $this->salesChannelRepository->search(new Criteria([$id]), Context::createDefaultContext())->first();

        static::assertNotNull($salesChannel);
        static::assertEquals('test', $salesChannel->getName());
        static::assertEquals(Defaults::SALES_CHANNEL_TYPE_API, $salesChannel->getTypeId());
    }
}
