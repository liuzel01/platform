<?php declare(strict_types=1);

namespace Shuwei\Core\Framework\Api\OAuth;

use League\OAuth2\Server\Entities\AccessTokenEntityInterface;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Repositories\AccessTokenRepositoryInterface;
use Shuwei\Core\Framework\Api\OAuth\Client\ApiClient;
use Shuwei\Core\Framework\Log\Package;

#[Package('core')]
class AccessTokenRepository implements AccessTokenRepositoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function getNewToken(ClientEntityInterface $clientEntity, array $scopes, $userIdentifier = null): AccessTokenEntityInterface
    {
        $token = new AccessToken($clientEntity, $scopes, $userIdentifier);

        if ($clientEntity instanceof ApiClient && $clientEntity->getIdentifier() === 'administration') {
            $token->setIdentifier('administration');
        }

        return $token;
    }

    /**
     * {@inheritdoc}
     */
    public function persistNewAccessToken(AccessTokenEntityInterface $accessTokenEntity): void
    {
    }

    /**
     * {@inheritdoc}
     */
    public function revokeAccessToken($tokenId): void
    {
    }

    /**
     * {@inheritdoc}
     */
    public function isAccessTokenRevoked($tokenId): bool
    {
        return false;
    }
}
