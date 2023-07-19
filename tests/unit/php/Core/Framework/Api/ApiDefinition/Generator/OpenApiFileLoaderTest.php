<?php declare(strict_types=1);

namespace Shuwei\Tests\Unit\Core\Framework\Api\ApiDefinition\Generator;

use PHPUnit\Framework\TestCase;
use Shuwei\Core\Framework\Api\ApiDefinition\Generator\OpenApiFileLoader;

/**
 * @covers \Shuwei\Core\Framework\Api\ApiDefinition\Generator\OpenApiFileLoader
 *
 * @internal
 */
class OpenApiFileLoaderTest extends TestCase
{

    public function testEmptyFileLoader(): void
    {
        $fsLoader = new OpenApiFileLoader([]);

        $spec = $fsLoader->loadOpenapiSpecification();

        static::assertSame(
            [
                'paths' => [],
                'components' => [],
            ],
            $spec
        );
    }
}
