<?php declare(strict_types=1);

namespace Shuwei\Core\Framework\Test\Store\Authentication;

use PHPUnit\Framework\TestCase;
use Shuwei\Core\Framework\Api\Context\AdminApiSource;
use Shuwei\Core\Framework\Api\Context\Exception\InvalidContextSourceUserException;
use Shuwei\Core\Framework\Context;
use Shuwei\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shuwei\Core\Framework\Store\Authentication\AbstractStoreRequestOptionsProvider;
use Shuwei\Core\Framework\Store\Authentication\StoreRequestOptionsProvider;
use Shuwei\Core\Framework\Test\Store\StoreClientBehaviour;
use Shuwei\Core\Framework\Test\TestCaseBase\IntegrationTestBehaviour;
use Shuwei\Core\Framework\Uuid\Uuid;

/**
 * @internal
 */
class StoreRequestOptionsProviderTest extends TestCase
{
    use IntegrationTestBehaviour;
    use StoreClientBehaviour;

    private AbstractStoreRequestOptionsProvider $storeRequestOptionsProvider;

    private Context $storeContext;

    protected function setUp(): void
    {
        $this->storeRequestOptionsProvider = $this->getContainer()->get(StoreRequestOptionsProvider::class);
        $this->storeContext = $this->createAdminStoreContext();
    }

    public function testGetAuthenticationHeadersHasUserStoreTokenAndShopSecret(): void
    {
        $shopSecret = 'im-a-super-safe-secret';

        $this->setShopSecret($shopSecret);
        $headers = $this->storeRequestOptionsProvider->getAuthenticationHeader($this->storeContext);

        static::assertEquals([
            'X-Shuwei-Platform-Token' => $this->getStoreTokenFromContext($this->storeContext),
            'X-Shuwei-Shop-Secret' => $shopSecret,
        ], $headers);
    }

    public function testGetAuthenticationHeadersUsesFirstStoreTokenFoundIfContextIsSystemSource(): void
    {
        $shopSecret = 'im-a-super-safe-secret';

        $this->setShopSecret($shopSecret);
        $headers = $this->storeRequestOptionsProvider->getAuthenticationHeader(Context::createDefaultContext());

        static::assertEquals([
            'X-Shuwei-Platform-Token' => $this->getStoreTokenFromContext($this->storeContext),
            'X-Shuwei-Shop-Secret' => $shopSecret,
        ], $headers);
    }

    public function testGetAuthenticationHeadersThrowsForIntegrations(): void
    {
        $context = new Context(new AdminApiSource(null, Uuid::randomHex()));

        static::expectException(InvalidContextSourceUserException::class);
        $this->storeRequestOptionsProvider->getAuthenticationHeader($context);
    }

    public function testGetDefaultQueriesReturnsLanguageFromContext(): void
    {
        $queries = $this->storeRequestOptionsProvider->getDefaultQueryParameters($this->storeContext);

        static::assertArrayHasKey('language', $queries);
        static::assertEquals(
            $this->getLanguageFromContext($this->storeContext),
            $queries['language']
        );
    }

    public function testGetDefaultQueriesReturnsShuweiVersion(): void
    {
        $queries = $this->storeRequestOptionsProvider->getDefaultQueryParameters($this->storeContext);

        static::assertArrayHasKey('shuweiVersion', $queries);
        static::assertEquals($this->getShuweiVersion(), $queries['shuweiVersion']);
    }

    public function testGetDefaultQueriesDoesHaveDomainSetEvenIfLicenseDomainIsNull(): void
    {
        $this->setLicenseDomain(null);

        $queries = $this->storeRequestOptionsProvider->getDefaultQueryParameters($this->storeContext);

        static::assertArrayHasKey('domain', $queries);
        static::assertEquals('', $queries['domain']);
    }

    public function testGetDefaultQueriesDoesHaveDomainSetIfLicenseDomainIsSet(): void
    {
        $this->setLicenseDomain('shuwei.swag');

        $queries = $this->storeRequestOptionsProvider->getDefaultQueryParameters($this->storeContext);

        static::assertArrayHasKey('domain', $queries);
        static::assertEquals('shuwei.swag', $queries['domain']);
    }

    public function testGetDefaultQueriesWithLicenseDomain(): void
    {
        $this->setLicenseDomain('new-license-domain');

        $queries = $this->storeRequestOptionsProvider->getDefaultQueryParameters($this->storeContext);

        static::assertArrayHasKey('domain', $queries);
        static::assertEquals('new-license-domain', $queries['domain']);
    }

    private function getLanguageFromContext(Context $context): string
    {
        /** @var AdminApiSource $contextSource */
        $contextSource = $context->getSource();
        $userId = $contextSource->getUserId();

        static::assertIsString($userId);

        $criteria = (new Criteria([$userId]))->addAssociation('locale');

        $user = $this->getUserRepository()->search($criteria, $context)->first();

        return $user->getLocale()->getCode();
    }
}
