<?php declare(strict_types=1);

namespace Shuwei\Core\Framework\Test\Store\Authentication;

use PHPUnit\Framework\TestCase;
use Shuwei\Core\Framework\Api\Context\AdminApiSource;
use Shuwei\Core\Framework\Api\Context\Exception\InvalidContextSourceException;
use Shuwei\Core\Framework\Context;
use Shuwei\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shuwei\Core\Framework\Store\Authentication\FrwRequestOptionsProvider;
use Shuwei\Core\Framework\Store\Services\FirstRunWizardService;
use Shuwei\Core\Framework\Test\Store\StoreClientBehaviour;
use Shuwei\Core\Framework\Test\TestCaseBase\IntegrationTestBehaviour;

/**
 * @internal
 */
class FrwRequestOptionsProviderTest extends TestCase
{
    use IntegrationTestBehaviour;
    use StoreClientBehaviour;

    private Context $context;

    private FrwRequestOptionsProvider $optionsProvider;

    private EntityRepository $userConfigRepository;

    protected function setUp(): void
    {
        $this->context = $this->createAdminStoreContext();
        $this->optionsProvider = $this->getContainer()->get(FrwRequestOptionsProvider::class);
        $this->userConfigRepository = $this->getContainer()->get('user_config.repository');
    }

    public function testSetsFrwUserTokenIfPresentInUserConfig(): void
    {
        $frwUserToken = 'a84a653a57dc43a48ded4275524893cf';

        $source = $this->context->getSource();
        static::assertInstanceOf(AdminApiSource::class, $source);

        $this->userConfigRepository->create([
            [
                'userId' => $source->getUserId(),
                'key' => FirstRunWizardService::USER_CONFIG_KEY_FRW_USER_TOKEN,
                'value' => [
                    FirstRunWizardService::USER_CONFIG_VALUE_FRW_USER_TOKEN => $frwUserToken,
                ],
            ],
        ], Context::createDefaultContext());

        $headers = $this->optionsProvider->getAuthenticationHeader($this->context);

        static::assertArrayHasKey('X-Shuwei-Token', $headers);
        static::assertEquals($frwUserToken, $headers['X-Shuwei-Token']);
    }

    public function testRemovesEmptyAuthenticationHeaderIfFrwUserTokenIsNotSet(): void
    {
        $headers = $this->optionsProvider->getAuthenticationHeader($this->context);

        static::assertEmpty($headers);
    }

    public function testThrowsInvalidContextSourceExceptionIfNotAdminApiSource(): void
    {
        static::expectException(InvalidContextSourceException::class);

        $this->optionsProvider->getAuthenticationHeader(Context::createDefaultContext());
    }
}
