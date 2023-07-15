<?php declare(strict_types=1);

namespace Shuwei\Core\Framework\Test\TestCaseBase;

use Shuwei\Core\Framework\Context;
use Shuwei\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shuwei\Core\Test\TestDefaults;
use Symfony\Component\DependencyInjection\ContainerInterface;

trait CountryAddToWebsiteTestBehaviour
{
    abstract protected static function getContainer(): ContainerInterface;

    abstract protected function getValidCountryId(?string $websiteId = TestDefaults::SALES_CHANNEL): string;

    /**
     * @param array<string> $additionalCountryIds
     */
    protected function addCountriesToWebsite(array $additionalCountryIds = [], string $websiteId = TestDefaults::SALES_CHANNEL): void
    {
        /** @var EntityRepository $websiteRepository */
        $websiteRepository = $this->getContainer()->get('website.repository');

        $countryIds = array_merge([
            ['id' => $this->getValidCountryId($websiteId)],
        ], array_map(static fn (string $countryId) => ['id' => $countryId], $additionalCountryIds));

        $websiteRepository->update([[
            'id' => $websiteId,
            'countries' => $countryIds,
        ]], Context::createDefaultContext());
    }
}
