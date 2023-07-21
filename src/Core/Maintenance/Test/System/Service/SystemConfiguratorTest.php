<?php declare(strict_types=1);

namespace Shuwei\Core\Maintenance\Test\System\Service;

use PHPUnit\Framework\TestCase;
use Shuwei\Core\Defaults;
use Shuwei\Core\Framework\Context;
use Shuwei\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shuwei\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shuwei\Core\Framework\Log\Package;
use Shuwei\Core\Framework\Test\TestCaseBase\IntegrationTestBehaviour;
use Shuwei\Core\Maintenance\System\Service\SystemConfigurator;
use Shuwei\Core\System\SystemConfig\SystemConfigService;

/**
 * @internal
 */
#[Package('system-settings')]
class SystemConfiguratorTest extends TestCase
{
    use IntegrationTestBehaviour;

    private SystemConfigurator $shopConfigurator;

    private SystemConfigService $systemConfigService;

    protected function setUp(): void
    {
        $this->shopConfigurator = $this->getContainer()->get(SystemConfigurator::class);
        $this->systemConfigService = $this->getContainer()->get(SystemConfigService::class);
    }

    public function testUpdateBasicInformation(): void
    {
        $this->shopConfigurator->updateBasicInformation('test-shop', 'shop@test.com');

        static::assertEquals('test-shop', $this->systemConfigService->get('core.basicInformation.shopName'));
        static::assertEquals('shop@test.com', $this->systemConfigService->get('core.basicInformation.email'));
    }

    public function testSwitchLanguageWithNewLanguage(): void
    {
        $this->shopConfigurator->setDefaultLanguage('zh-CN');

        /** @var EntityRepository $langRepo */
        $langRepo = $this->getContainer()->get('language.repository');

        $lang = $langRepo->search(new Criteria([Defaults::LANGUAGE_SYSTEM]), Context::createDefaultContext())
            ->first();

        static::assertEquals('中文', $lang->getName());
    }

    public function testSwitchLanguageWithDefaultLocale(): void
    {
        $this->shopConfigurator->setDefaultLanguage('zh-CN');

        /** @var EntityRepository $langRepo */
        $langRepo = $this->getContainer()->get('language.repository');

        $lang = $langRepo->search(new Criteria([Defaults::LANGUAGE_SYSTEM]), Context::createDefaultContext())
            ->first();

        static::assertEquals('中文', $lang->getName());
    }

    public function testSwitchLanguageWithExistingLanguage(): void
    {
        $this->shopConfigurator->setDefaultLanguage('zh-CN');

        /** @var EntityRepository $langRepo */
        $langRepo = $this->getContainer()->get('language.repository');

        $lang = $langRepo->search(new Criteria([Defaults::LANGUAGE_SYSTEM]), Context::createDefaultContext())
            ->first();

        static::assertEquals('中文', $lang->getName());
    }

}
