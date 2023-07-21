<?php declare(strict_types=1);

namespace Shuwei\Core\Framework\Test\Event\EventData;

use PHPUnit\Framework\TestCase;
use Shuwei\Core\Framework\Event\EventData\EntityCollectionType;
use Shuwei\Core\System\User\UserDefinition;

/**
 * @internal
 */
class EntityCollectionTypeTest extends TestCase
{
    public function testToArray(): void
    {
        $expected = [
            'type' => 'collection',
            'entityClass' => UserDefinition::class,
        ];

        static::assertEquals($expected, (new EntityCollectionType(UserDefinition::class))->toArray());
    }
}
