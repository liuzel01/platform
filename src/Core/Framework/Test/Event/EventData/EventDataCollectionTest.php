<?php declare(strict_types=1);

namespace Shuwei\Core\Framework\Test\Event\EventData;

use PHPUnit\Framework\TestCase;
use Shuwei\Core\Framework\Event\EventData\EntityType;
use Shuwei\Core\Framework\Event\EventData\EventDataCollection;
use Shuwei\Core\Framework\Event\EventData\ScalarValueType;
use Shuwei\Core\System\User\UserDefinition;

/**
 * @internal
 */
class EventDataCollectionTest extends TestCase
{
    public function testToArray(): void
    {
        $collection = (new EventDataCollection())
            ->add('user', new EntityType(UserDefinition::class))
            ->add('myBool', new ScalarValueType(ScalarValueType::TYPE_BOOL))
        ;

        $expected = [
            'user' => [
                'type' => 'entity',
                'entityClass' => UserDefinition::class,
            ],
            'myBool' => [
                'type' => 'bool',
            ],
        ];

        static::assertEquals($expected, $collection->toArray());
    }
}
