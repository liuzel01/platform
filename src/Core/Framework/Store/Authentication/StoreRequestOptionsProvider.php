<?php declare(strict_types=1);

namespace Shuwei\Core\Framework\Store\Authentication;

use Shuwei\Core\Framework\Api\Context\Exception\InvalidContextSourceException;
use Shuwei\Core\Framework\Api\Context\Exception\InvalidContextSourceUserException;
use Shuwei\Core\Framework\Api\Context\SystemSource;
use Shuwei\Core\Framework\Context;
use Shuwei\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shuwei\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shuwei\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shuwei\Core\Framework\DataAbstractionLayer\Search\Filter\NotFilter;
use Shuwei\Core\Framework\Log\Package;
use Shuwei\Core\Framework\Store\Services\InstanceService;
use Shuwei\Core\System\SystemConfig\SystemConfigService;
use Shuwei\Core\System\User\UserEntity;

/**
 * @internal
 */
#[Package('merchant-services')]
class StoreRequestOptionsProvider extends AbstractStoreRequestOptionsProvider
{
    final public const CONFIG_KEY_STORE_LICENSE_DOMAIN = 'core.store.licenseHost';
    final public const CONFIG_KEY_STORE_SHOP_SECRET = 'core.store.shopSecret';

    private const SHUWEI_PLATFORM_TOKEN_HEADER = 'X-Shuwei-Platform-Token';
    private const SHUWEI_SHOP_SECRET_HEADER = 'X-Shuwei-Shop-Secret';

    public function __construct(
        private readonly EntityRepository $userRepository,
        private readonly SystemConfigService $systemConfigService,
        private readonly InstanceService $instanceService,
        private readonly LocaleProvider $localeProvider,
    ) {
    }

    /**
     * @return array<string, string>
     */
    public function getAuthenticationHeader(Context $context): array
    {
        return array_filter([
            self::SHUWEI_PLATFORM_TOKEN_HEADER => $this->getUserStoreToken($context),
            self::SHUWEI_SHOP_SECRET_HEADER => $this->systemConfigService->getString(self::CONFIG_KEY_STORE_SHOP_SECRET),
        ]);
    }

    /**
     * @return array<string, string>
     */
    public function getDefaultQueryParameters(Context $context): array
    {
        return [
            'shuweiVersion' => $this->instanceService->getShuweiVersion(),
            'language' => $this->localeProvider->getLocaleFromContext($context),
            'domain' => $this->getLicenseDomain(),
        ];
    }

    private function getUserStoreToken(Context $context): ?string
    {
        try {
            return $this->getTokenFromAdmin($context);
        } catch (InvalidContextSourceException) {
            return $this->getTokenFromSystem($context);
        }
    }

    private function getTokenFromAdmin(Context $context): ?string
    {
        $contextSource = $this->ensureAdminApiSource($context);
        $userId = $contextSource->getUserId();
        if ($userId === null) {
            throw new InvalidContextSourceUserException($contextSource::class);
        }

        return $this->fetchUserStoreToken(new Criteria([$userId]), $context);
    }

    private function getTokenFromSystem(Context $context): ?string
    {
        $contextSource = $context->getSource();
        if (!($contextSource instanceof SystemSource)) {
            throw new InvalidContextSourceException(SystemSource::class, $contextSource::class);
        }

        $criteria = new Criteria();
        $criteria->addFilter(
            new NotFilter(NotFilter::CONNECTION_OR, [new EqualsFilter('storeToken', null)])
        );

        return $this->fetchUserStoreToken($criteria, $context);
    }

    private function fetchUserStoreToken(Criteria $criteria, Context $context): ?string
    {
        /** @var UserEntity|null $user */
        $user = $this->userRepository->search($criteria, $context)->first();

        if ($user === null) {
            return null;
        }

        return $user->getStoreToken();
    }

    private function getLicenseDomain(): string
    {
        /** @var string $domain */
        $domain = $this->systemConfigService->get(self::CONFIG_KEY_STORE_LICENSE_DOMAIN) ?? '';

        return $domain;
    }
}
