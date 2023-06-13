<?php declare(strict_types=1);

namespace Shuwei\Core\Maintenance\Test\SalesChannel\Command;

use PHPUnit\Framework\TestCase;
use Shuwei\Core\Defaults;
use Shuwei\Core\DevOps\Environment\EnvironmentHelper;
use Shuwei\Core\Framework\Context;
use Shuwei\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shuwei\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shuwei\Core\Framework\Log\Package;
use Shuwei\Core\Framework\Test\TestCaseBase\IntegrationTestBehaviour;
use Shuwei\Core\Maintenance\SalesChannel\Command\SalesChannelUpdateDomainCommand;
use Shuwei\Core\System\SalesChannel\Aggregate\SalesChannelDomain\SalesChannelDomainEntity;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * @internal
 */
#[Package('core')]
class SalesChannelUpdateDomainCommandTest extends TestCase
{
    use IntegrationTestBehaviour;

    public function testUpdateDomainCommand(): void
    {
        $commandTester = new CommandTester($this->getContainer()->get(SalesChannelUpdateDomainCommand::class));
        $commandTester->execute(['domain' => 'test.de']);

        static::assertEquals(
            0,
            $commandTester->getStatusCode(),
            "\"bin/console sales-channel:maintenance:disable\" returned errors:\n" . $commandTester->getDisplay()
        );

        $domainRepo = $this->getContainer()->get('sales_channel_domain.repository');
        $criteria = new Criteria();
        $criteria->addFilter(new EqualsFilter('salesChannel.typeId', Defaults::SALES_CHANNEL_TYPE_STOREFRONT));

        /** @var SalesChannelDomainEntity $domain */
        $domain = $domainRepo->search($criteria, Context::createDefaultContext())->first();

        static::assertSame('test.de', parse_url($domain->getUrl(), \PHP_URL_HOST));
    }

    public function testUpdateWithRandomPreviousDomain(): void
    {
        $commandTester = new CommandTester($this->getContainer()->get(SalesChannelUpdateDomainCommand::class));
        $commandTester->execute(['domain' => 'test.de', '--previous-domain' => 'shop.test']);

        static::assertEquals(
            0,
            $commandTester->getStatusCode(),
            "\"bin/console sales-channel:maintenance:disable\" returned errors:\n" . $commandTester->getDisplay()
        );

        $domainRepo = $this->getContainer()->get('sales_channel_domain.repository');
        $criteria = new Criteria();
        $criteria->addFilter(new EqualsFilter('salesChannel.typeId', Defaults::SALES_CHANNEL_TYPE_STOREFRONT));

        /** @var SalesChannelDomainEntity $domain */
        $domain = $domainRepo->search($criteria, Context::createDefaultContext())->first();

        $defaultDomain = parse_url((string) EnvironmentHelper::getVariable('APP_URL'), \PHP_URL_HOST);
        static::assertSame($defaultDomain, parse_url($domain->getUrl(), \PHP_URL_HOST));
    }

    public function testUpdateWithCorrectPreviousDomain(): void
    {
        $defaultHost = parse_url((string) EnvironmentHelper::getVariable('APP_URL'), \PHP_URL_HOST);

        $commandTester = new CommandTester($this->getContainer()->get(SalesChannelUpdateDomainCommand::class));
        $commandTester->execute(['domain' => 'test.de', '--previous-domain' => $defaultHost]);

        static::assertEquals(
            0,
            $commandTester->getStatusCode(),
            "\"bin/console sales-channel:maintenance:disable\" returned errors:\n" . $commandTester->getDisplay()
        );

        $domainRepo = $this->getContainer()->get('sales_channel_domain.repository');
        $criteria = new Criteria();
        $criteria->addFilter(new EqualsFilter('salesChannel.typeId', Defaults::SALES_CHANNEL_TYPE_STOREFRONT));

        /** @var SalesChannelDomainEntity $domain */
        $domain = $domainRepo->search($criteria, Context::createDefaultContext())->first();

        static::assertSame('test.de', parse_url($domain->getUrl(), \PHP_URL_HOST));
    }
}
