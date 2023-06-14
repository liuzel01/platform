<?php declare(strict_types=1);

namespace Shuwei\Core\Framework\Test\Store;

use GuzzleHttp\Handler\MockHandler;
use Shuwei\Core\Framework\Api\Context\AdminApiSource;
use Shuwei\Core\Framework\Context;
use Shuwei\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shuwei\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shuwei\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shuwei\Core\Framework\Log\Package;
use Shuwei\Core\Framework\Store\Services\FirstRunWizardService;
use Shuwei\Core\Framework\Uuid\Uuid;
use Shuwei\Core\Kernel;
use Shuwei\Core\System\SystemConfig\SystemConfigService;
use Shuwei\Core\System\User\Aggregate\UserConfig\UserConfigEntity;
use Shuwei\Core\System\User\UserCollection;
use Shuwei\Core\System\User\UserEntity;

/**
 * @internal
 */
#[Package('merchant-services')]
trait StoreClientBehaviour
{
    /**
     * @deprecated tag:v6.6.0 - Will be removed, use ::getStoreRequestHandler() instead
     */
    public function getRequestHandler(): MockHandler
    {
        return $this->getStoreRequestHandler();
    }

    public function getStoreRequestHandler(): MockHandler
    {
        /** @var MockHandler $handler */
        $handler = $this->getContainer()->get('shuwei.store.mock_handler');

        return $handler;
    }

    public function getFrwRequestHandler(): MockHandler
    {
        /** @var MockHandler $handler */
        $handler = $this->getContainer()->get('shuwei.frw.mock_handler');

        return $handler;
    }

    /**
     * @after
     *
     * @before
     */
    public function resetStoreMock(): void
    {
        $this->getStoreRequestHandler()->reset();
    }

    /**
     * @after
     *
     * @before
     */
    public function resetFrwMock(): void
    {
        $this->getFrwRequestHandler()->reset();
    }

    protected function createAdminStoreContext(): Context
    {
        $userId = Uuid::randomHex();
        $storeToken = Uuid::randomHex();

        $data = [
            [
                'id' => $userId,
                'localeId' => $this->getLocaleIdOfSystemLanguage(),
                'username' => 'foobar',
                'password' => 'asdasdasdasd',
                'firstName' => 'Foo',
                'lastName' => 'Bar',
                'email' => Uuid::randomHex() . '@bar.com',
                'storeToken' => $storeToken,
            ],
        ];

        $this->getUserRepository()->create($data, Context::createDefaultContext());

        $source = new AdminApiSource($userId);
        $source->setIsAdmin(true);

        return Context::createDefaultContext($source);
    }

    protected function getStoreTokenFromContext(Context $context): string
    {
        /** @var AdminApiSource $source */
        $source = $context->getSource();

        $userId = $source->getUserId();

        if ($userId === null) {
            throw new \RuntimeException('No user id found in context');
        }

        /** @var UserCollection $users */
        $users = $this->getUserRepository()->search(new Criteria([$userId]), $context)->getEntities();

        if ($users->count() === 0) {
            throw new \RuntimeException('No user found with id ' . $userId);
        }

        $user = $users->first();
        static::assertInstanceOf(UserEntity::class, $user);

        $token = $user->getStoreToken();
        static::assertIsString($token);

        return $token;
    }

    protected function getFrwUserTokenFromContext(Context $context): ?string
    {
        /** @var AdminApiSource $source */
        $source = $context->getSource();
        $criteria = (new Criteria())->addFilter(
            new EqualsFilter('userId', $source->getUserId()),
            new EqualsFilter('key', FirstRunWizardService::USER_CONFIG_KEY_FRW_USER_TOKEN),
        );

        /** @var UserConfigEntity|null $config */
        $config = $this->getContainer()->get('user_config.repository')->search($criteria, $context)->first();

        return $config ? $config->getValue()[FirstRunWizardService::USER_CONFIG_VALUE_FRW_USER_TOKEN] ?? null : null;
    }

    protected function setFrwUserToken(Context $context, string $frwUserToken): void
    {
        $source = $context->getSource();
        if (!$source instanceof AdminApiSource) {
            throw new \RuntimeException('Context with AdminApiSource expected.');
        }

        $this->getContainer()->get('user_config.repository')->create([
            [
                'userId' => $source->getUserId(),
                'key' => FirstRunWizardService::USER_CONFIG_KEY_FRW_USER_TOKEN,
                'value' => [
                    FirstRunWizardService::USER_CONFIG_VALUE_FRW_USER_TOKEN => $frwUserToken,
                ],
            ],
        ], Context::createDefaultContext());
    }

    protected function setLicenseDomain(?string $licenseDomain): void
    {
        $systemConfigService = $this->getContainer()->get(SystemConfigService::class);

        $systemConfigService->set(
            'core.store.licenseHost',
            $licenseDomain
        );
    }

    protected function setShopSecret(string $shopSecret): void
    {
        $systemConfigService = $this->getContainer()->get(SystemConfigService::class);

        $systemConfigService->set(
            'core.store.shopSecret',
            $shopSecret
        );
    }

    protected function getShuweiVersion(): string
    {
        $version = $this->getContainer()->getParameter('kernel.shuwei_version');

        return $version === Kernel::SHUWEI_FALLBACK_VERSION ? '___VERSION___' : $version;
    }

    protected function getUserRepository(): EntityRepository
    {
        return $this->getContainer()->get('user.repository');
    }
}
