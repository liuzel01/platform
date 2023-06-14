<?php declare(strict_types=1);

namespace Shuwei\Core\Framework\Test\Store\Api;

use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Shuwei\Core\Framework\Api\Context\AdminApiSource;
use Shuwei\Core\Framework\Context;
use Shuwei\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shuwei\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shuwei\Core\Framework\Store\Api\StoreController;
use Shuwei\Core\Framework\Store\Exception\StoreApiException;
use Shuwei\Core\Framework\Store\Exception\StoreInvalidCredentialsException;
use Shuwei\Core\Framework\Store\Services\AbstractExtensionDataProvider;
use Shuwei\Core\Framework\Store\Services\StoreClient;
use Shuwei\Core\Framework\Store\Struct\PluginDownloadDataStruct;
use Shuwei\Core\Framework\Test\TestCaseBase\IntegrationTestBehaviour;
use Shuwei\Core\Framework\Test\TestCaseBase\KernelTestBehaviour;
use Shuwei\Core\Framework\Uuid\Uuid;
use Shuwei\Core\System\User\UserEntity;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * @internal
 */
class StoreControllerTest extends TestCase
{
    use IntegrationTestBehaviour;
    use KernelTestBehaviour;

    private Context $defaultContext;

    private EntityRepository $userRepository;

    protected function setUp(): void
    {
        $this->defaultContext = Context::createDefaultContext();
        $this->userRepository = $this->getContainer()->get('user.repository');
    }

    public function testCheckLoginWithoutStoreToken(): void
    {
        /** @var UserEntity $adminUser */
        $adminUser = $this->userRepository->search(new Criteria(), $this->defaultContext)->first();

        $storeController = $this->getStoreController();
        $context = new Context(new AdminApiSource($adminUser->getId()));

        $response = $storeController->checkLogin($context)->getContent();
        static::assertIsString($response);

        $response = json_decode($response, true, 512, \JSON_THROW_ON_ERROR);
        static::assertNull($response['userInfo']);
    }

    public function testLoginWithCorrectCredentials(): void
    {
        $request = new Request([], [
            'shuweiId' => 'j.doe@shuwei.com',
            'password' => 'v3rys3cr3t',
        ]);

        /** @var UserEntity $adminUser */
        $adminUser = $this->userRepository->search(new Criteria(), $this->defaultContext)->first();

        $context = new Context(new AdminApiSource($adminUser->getId()));

        $storeClientMock = $this->createMock(StoreClient::class);
        $storeClientMock->expects(static::once())
            ->method('loginWithShuweiId')
            ->with('j.doe@shuwei.com', 'v3rys3cr3t');

        $storeController = $this->getStoreController($storeClientMock);

        $response = $storeController->login($request, $context);

        static::assertInstanceOf(JsonResponse::class, $response);
        static::assertSame(200, $response->getStatusCode());
    }

    public function testLoginWithInvalidCredentials(): void
    {
        $request = new Request([], [
            'shuweiId' => 'j.doe@shuwei.com',
            'password' => 'v3rys3cr3t',
        ]);

        /** @var UserEntity $adminUser */
        $adminUser = $this->userRepository->search(new Criteria(), $this->defaultContext)->first();

        $context = new Context(new AdminApiSource($adminUser->getId()));

        $clientExceptionMock = $this->createMock(ClientException::class);
        $clientExceptionMock->method('getResponse')
            ->willReturn(new Response());

        $storeClientMock = $this->createMock(StoreClient::class);
        $storeClientMock->expects(static::once())
            ->method('loginWithShuweiId')
            ->willThrowException($clientExceptionMock);

        $storeController = $this->getStoreController($storeClientMock);

        static::expectException(StoreApiException::class);
        $storeController->login($request, $context);
    }

    public function testLoginWithInvalidCredentialsInput(): void
    {
        $request = new Request([], [
            'shuweiId' => null,
            'password' => null,
        ]);

        /** @var UserEntity $adminUser */
        $adminUser = $this->userRepository->search(new Criteria(), $this->defaultContext)->first();

        $context = new Context(new AdminApiSource($adminUser->getId()));

        $storeClientMock = $this->createMock(StoreClient::class);
        $storeClientMock->expects(static::never())
            ->method('loginWithShuweiId');

        $storeController = $this->getStoreController($storeClientMock);

        static::expectException(StoreInvalidCredentialsException::class);
        $storeController->login($request, $context);
    }

    public function testCheckLoginWithStoreToken(): void
    {
        /** @var UserEntity $adminUser */
        $adminUser = $this->userRepository->search(new Criteria(), $this->defaultContext)->first();

        $this->userRepository->update([[
            'id' => $adminUser->getId(),
            'storeToken' => 'store-token',
        ]], $this->defaultContext);

        $storeController = $this->getStoreController();
        $context = new Context(new AdminApiSource($adminUser->getId()));

        $response = $storeController->checkLogin($context)->getContent();
        static::assertIsString($response);

        $response = json_decode($response, true, 512, \JSON_THROW_ON_ERROR);
        static::assertEquals($response['userInfo'], [
            'name' => 'John Doe',
            'email' => 'john.doe@shuwei.com',
        ]);
    }

    public function testCheckLoginWithMultipleStoreTokens(): void
    {
        /** @var UserEntity $adminUser */
        $adminUser = $this->userRepository->search(new Criteria(), $this->defaultContext)->first();

        $this->userRepository->update([[
            'id' => $adminUser->getId(),
            'storeToken' => 'store-token',
            'firstName' => 'John',
        ]], $this->defaultContext);

        $this->userRepository->create([[
            'id' => Uuid::randomHex(),
            'firstName' => 'Jane',
            'lastName' => 'Doe',
            'storeToken' => 'store-token-two',
            'localeId' => $adminUser->getLocaleId(),
            'username' => 'admin-two',
            'password' => 's3cr3t12345',
            'email' => 'jane.doe@shuwei.com',
        ]], $this->defaultContext);

        $storeController = $this->getStoreController();
        $context = new Context(new AdminApiSource($adminUser->getId()));

        $response = $storeController->checkLogin($context)->getContent();
        static::assertIsString($response);

        $response = json_decode($response, true, 512, \JSON_THROW_ON_ERROR);
        static::assertEquals($response['userInfo'], [
            'name' => 'John Doe',
            'email' => 'john.doe@shuwei.com',
        ]);
    }

    private function getStoreController(
        ?StoreClient $storeClient = null,
    ): StoreController {
        return new StoreController(
            $storeClient ?? $this->getStoreClientMock(),
            $this->userRepository,
            $this->getContainer()->get(AbstractExtensionDataProvider::class)
        );
    }

    /**
     * @return StoreClient|MockObject
     */
    private function getStoreClientMock(): StoreClient
    {
        $storeClient = $this->getMockBuilder(StoreClient::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getDownloadDataForPlugin', 'userInfo'])
            ->getMock();

        $storeClient->method('getDownloadDataForPlugin')
            ->willReturn($this->getPluginDownloadDataStub());

        $storeClient->method('userInfo')
            ->willReturn([
                'name' => 'John Doe',
                'email' => 'john.doe@shuwei.com',
            ]);

        return $storeClient;
    }

    private function getPluginDownloadDataStub(): PluginDownloadDataStruct
    {
        return (new PluginDownloadDataStruct())
            ->assign([
                'location' => 'not-null',
            ]);
    }
}
