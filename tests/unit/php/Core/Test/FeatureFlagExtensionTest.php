<?php declare(strict_types=1);

namespace Shuwei\Tests\Unit\Core\Test;

use PHPUnit\Framework\TestCase;
use Shuwei\Core\Framework\Feature;
use Shuwei\Core\Test\Annotation\DisabledFeatures;
use Shuwei\Core\Test\FeatureFlagExtension;

/**
 * @internal
 *
 * @covers \Shuwei\Core\Test\FeatureFlagExtension
 */
class FeatureFlagExtensionTest extends TestCase
{
    /**
     * @var array<string, mixed>
     */
    private array $serverVarsBackup;

    /**
     * @var array<string, mixed>
     */
    private array $envVarsBackup;

    /**
     * @var array<string, array{'name'?: string, 'default'?: boolean, 'major'?: boolean, 'description'?: string}>
     */
    private array $featureConfigBackup;

    private FeatureFlagExtension $extension;

    protected function setUp(): void
    {
        $this->serverVarsBackup = $_SERVER;
        $this->envVarsBackup = $_ENV;
        $this->featureConfigBackup = Feature::getRegisteredFeatures();
        $this->extension = new FeatureFlagExtension('Shuwei\\Tests\\Unit\\', true);
    }

    protected function tearDown(): void
    {
        $_SERVER = $this->serverVarsBackup;
        $_ENV = $this->envVarsBackup;
        Feature::resetRegisteredFeatures();
        Feature::registerFeatures($this->featureConfigBackup);
    }

    public function testAllFeatureFlagsAreActivated(): void
    {
        $_SERVER['V6_5_0_0'] = false;

        $this->extension->executeBeforeTest(__METHOD__);

        static::assertTrue(Feature::isActive('v6.5.0.0'));

        $this->extension->executeAfterTest(__METHOD__, 0.1);

        static::assertArrayHasKey('V6_5_0_0', $_SERVER);
        static::assertFalse($_SERVER['V6_5_0_0']);
    }


    public function testFeatureConfigAndEnvIsRestored(): void
    {
        $beforeFeatureFlagConfig = Feature::getRegisteredFeatures();
        $beforeServerEnv = $_SERVER;

        $this->extension->executeBeforeTest(__METHOD__);

        $_SERVER = ['asdf' => 'foo'];
        Feature::resetRegisteredFeatures();
        Feature::registerFeature('foobar');

        $this->extension->executeAfterTest(__METHOD__, 0.1);

        static::assertSame($beforeFeatureFlagConfig, Feature::getRegisteredFeatures());
        static::assertSame($beforeServerEnv, $_SERVER);
    }

    /**
     * @DisabledFeatures(features={"v6.5.0.0"})
     */
    public function testSetsFeatures(): void
    {
        static::assertArrayNotHasKey('V6_5_0_0', $_SERVER);

        $this->extension->executeBeforeTest(__METHOD__);

        static::assertArrayHasKey('V6_5_0_0', $_SERVER);
        static::assertFalse($_SERVER['V6_5_0_0']);
        static::assertFalse(Feature::isActive('v6.5.0.0'));

        $this->extension->executeAfterTest(__METHOD__, 0.1);

        static::assertArrayNotHasKey('V6_5_0_0', $_SERVER);
    }
}
