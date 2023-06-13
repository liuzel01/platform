<?php declare(strict_types=1);

namespace Shuwei\Core\Framework\Store\Authentication;

use Shuwei\Core\Framework\Api\Context\AdminApiSource;
use Shuwei\Core\Framework\Api\Context\Exception\InvalidContextSourceException;
use Shuwei\Core\Framework\Context;
use Shuwei\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shuwei\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shuwei\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shuwei\Core\Framework\Log\Package;
use Shuwei\Core\Framework\Store\Services\FirstRunWizardService;
use Shuwei\Core\System\User\Aggregate\UserConfig\UserConfigEntity;

/**
 * @internal
 */
#[Package('merchant-services')]
class FrwRequestOptionsProvider extends AbstractStoreRequestOptionsProvider
{
    private const SHUWEI_TOKEN_HEADER = 'X-Shuwei-Token';

    public function __construct(
        private readonly AbstractStoreRequestOptionsProvider $optionsProvider,
        private readonly EntityRepository $userConfigRepository,
    ) {
    }

    public function getAuthenticationHeader(Context $context): array
    {
        return array_filter([self::SHUWEI_TOKEN_HEADER => $this->getFrwUserToken($context)]);
    }

    public function getDefaultQueryParameters(Context $context): array
    {
        return $this->optionsProvider->getDefaultQueryParameters($context);
    }

    private function getFrwUserToken(Context $context): ?string
    {
        if (!$context->getSource() instanceof AdminApiSource) {
            throw new InvalidContextSourceException(AdminApiSource::class, $context->getSource()::class);
        }

        /** @var AdminApiSource $contextSource */
        $contextSource = $context->getSource();

        $criteria = (new Criteria())->addFilter(
            new EqualsFilter('userId', $contextSource->getUserId()),
            new EqualsFilter('key', FirstRunWizardService::USER_CONFIG_KEY_FRW_USER_TOKEN),
        );

        /** @var UserConfigEntity|null $userConfig */
        $userConfig = $this->userConfigRepository->search($criteria, $context)->first();

        return $userConfig === null ? null : $userConfig->getValue()[FirstRunWizardService::USER_CONFIG_VALUE_FRW_USER_TOKEN] ?? null;
    }
}
