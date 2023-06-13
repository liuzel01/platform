<?php declare(strict_types=1);

namespace Shuwei\Core\Framework\Store\Services;

use Shuwei\Core\Framework\Api\Context\AdminApiSource;
use Shuwei\Core\Framework\Context;
use Shuwei\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shuwei\Core\Framework\Log\Package;
use Shuwei\Core\Framework\Store\Struct\AccessTokenStruct;

/**
 * @internal
 */
#[Package('merchant-services')]
class StoreService
{
    final public const CONFIG_KEY_STORE_LICENSE_DOMAIN = 'core.store.licenseHost';
    final public const CONFIG_KEY_STORE_LICENSE_EDITION = 'core.store.licenseEdition';

    final public function __construct(private readonly EntityRepository $userRepository)
    {
    }

    public function updateStoreToken(Context $context, AccessTokenStruct $accessToken): void
    {
        /** @var AdminApiSource $contextSource */
        $contextSource = $context->getSource();
        $userId = $contextSource->getUserId();

        $storeToken = $accessToken->getShopUserToken()->getToken();

        $context->scope(Context::SYSTEM_SCOPE, function ($context) use ($userId, $storeToken): void {
            $this->userRepository->update([['id' => $userId, 'storeToken' => $storeToken]], $context);
        });
    }
}
