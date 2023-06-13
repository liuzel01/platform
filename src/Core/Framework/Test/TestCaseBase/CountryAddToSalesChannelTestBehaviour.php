<?php declare(strict_types=1);

namespace Shuwei\Core\Framework\Test\TestCaseBase;

use Shuwei\Core\Framework\Context;
use Shuwei\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shuwei\Core\Test\TestDefaults;
use Symfony\Component\DependencyInjection\ContainerInterface;

trait CountryAddToSalesChannelTestBehaviour
{
    abstract protected static function getContainer(): ContainerInterface;

    abstract protected function getValidCountryId(?string $salesChannelId = TestDefaults::SALES_CHANNEL): string;

    /**
     * @param array<string> $additionalCountryIds
     */
    protected function addCountriesToSalesChannel(array $additionalCountryIds = [], string $salesChannelId = TestDefaults::SALES_CHANNEL): void
    {
        /** @var EntityRepository $salesChannelRepository */
        $salesChannelRepository = $this->getContainer()->get('sales_channel.repository');

        $countryIds = array_merge([
            ['id' => $this->getValidCountryId($salesChannelId)],
        ], array_map(static fn (string $countryId) => ['id' => $countryId], $additionalCountryIds));

        $salesChannelRepository->update([[
            'id' => $salesChannelId,
            'countries' => $countryIds,
        ]], Context::createDefaultContext());
    }
}
