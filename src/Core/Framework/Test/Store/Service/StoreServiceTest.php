<?php declare(strict_types=1);

namespace Shuwei\Core\Framework\Test\Store\Service;

use PHPUnit\Framework\TestCase;
use Shuwei\Core\Framework\Api\Context\AdminApiSource;
use Shuwei\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shuwei\Core\Framework\Store\Services\StoreService;
use Shuwei\Core\Framework\Store\Struct\AccessTokenStruct;
use Shuwei\Core\Framework\Store\Struct\ShopUserTokenStruct;
use Shuwei\Core\Framework\Test\Store\StoreClientBehaviour;
use Shuwei\Core\Framework\Test\TestCaseBase\IntegrationTestBehaviour;

/**
 * @internal
 */
class StoreServiceTest extends TestCase
{
    use IntegrationTestBehaviour;
    use StoreClientBehaviour;

    private StoreService $storeService;

    protected function setUp(): void
    {
        $this->storeService = $this->getContainer()->get(StoreService::class);
    }

    public function testUpdateStoreToken(): void
    {
        $adminStoreContext = $this->createAdminStoreContext();

        $newToken = 'updated-store-token';
        $accessTokenStruct = new AccessTokenStruct(
            new ShopUserTokenStruct(
                $newToken,
                new \DateTimeImmutable()
            )
        );

        $this->storeService->updateStoreToken(
            $adminStoreContext,
            $accessTokenStruct
        );

        /** @var AdminApiSource $adminSource */
        $adminSource = $adminStoreContext->getSource();
        /** @var string $userId */
        $userId = $adminSource->getUserId();
        $criteria = new Criteria([$userId]);

        $updatedUser = $this->getUserRepository()->search($criteria, $adminStoreContext)->first();

        static::assertEquals('updated-store-token', $updatedUser->getStoreToken());
    }
}
