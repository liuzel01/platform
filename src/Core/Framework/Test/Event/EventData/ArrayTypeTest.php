<?php declare(strict_types=1);

namespace Shuwei\Core\Framework\Test\Event\EventData;

use PHPUnit\Framework\TestCase;
use Shuwei\Core\Framework\Event\EventData\ArrayType;
use Shuwei\Core\Framework\Event\EventData\ScalarValueType;

/**
 * @internal
 */
class ArrayTypeTest extends TestCase
{
    public function testToArray(): void
    {
        $expected = [
            'type' => 'array',
            'of' => [
                'type' => 'string',
            ],
        ];

        static::assertEquals(
            $expected,
            (new ArrayType(new ScalarValueType(ScalarValueType::TYPE_STRING)))
                ->toArray()
        );
    }
}
