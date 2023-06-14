<?php declare(strict_types=1);

namespace Shuwei\Core\Framework\Test\Store\Service;

use PHPUnit\Framework\TestCase;
use Shuwei\Core\Framework\Store\Services\InstanceService;
use Shuwei\Core\Kernel;

/**
 * @internal
 */
class InstanceServiceTest extends TestCase
{
    public function testItReturnsInstanceIdIfNull(): void
    {
        $instanceService = new InstanceService(
            '6.4.0.0',
            null
        );

        static::assertNull($instanceService->getInstanceId());
    }

    public function testItReturnsInstanceIdIfSet(): void
    {
        $instanceService = new InstanceService(
            '6.4.0.0',
            'i-am-unique'
        );

        static::assertEquals('i-am-unique', $instanceService->getInstanceId());
    }

    public function testItReturnsSpecificShuweiVersion(): void
    {
        $instanceService = new InstanceService(
            '6.1.0.0',
            null
        );

        static::assertEquals('6.1.0.0', $instanceService->getShuweiVersion());
    }

    public function testItReturnsShuweiVersionStringIfVersionIsDeveloperVersion(): void
    {
        $instanceService = new InstanceService(
            Kernel::SHUWEI_FALLBACK_VERSION,
            null
        );

        static::assertEquals('___VERSION___', $instanceService->getShuweiVersion());
    }
}
