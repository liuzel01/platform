<?php declare(strict_types=1);

namespace Shuwei\Core\Framework\Api\EventListener\Authentication;

use Doctrine\DBAL\Connection;
use Shuwei\Core\Defaults;
use Shuwei\Core\Framework\Api\OAuth\RefreshTokenRepository;
use Shuwei\Core\Framework\DataAbstractionLayer\Event\EntityDeletedEvent;
use Shuwei\Core\Framework\DataAbstractionLayer\Event\EntityWrittenEvent;
use Shuwei\Core\Framework\Log\Package;
use Shuwei\Core\Framework\Uuid\Uuid;
use Shuwei\Core\System\User\UserEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * @internal
 */
#[Package('core')]
class UserCredentialsChangedSubscriber implements EventSubscriberInterface
{
    /**
     * @internal
     */
    public function __construct(
        private readonly RefreshTokenRepository $refreshTokenRepository,
        private readonly Connection $connection
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            UserEvents::USER_WRITTEN_EVENT => 'onUserWritten',
            UserEvents::USER_DELETED_EVENT => 'onUserDeleted',
        ];
    }

    public function onUserWritten(EntityWrittenEvent $event): void
    {
        $payloads = $event->getPayloads();

        foreach ($payloads as $payload) {
            if ($this->userCredentialsChanged($payload)) {
                $this->refreshTokenRepository->revokeRefreshTokensForUser($payload['id']);
                $this->updateLastUpdatedPasswordTimestamp($payload['id']);
            }
        }
    }

    public function onUserDeleted(EntityDeletedEvent $event): void
    {
        $ids = $event->getIds();

        foreach ($ids as $id) {
            $this->refreshTokenRepository->revokeRefreshTokensForUser($id);
        }
    }

    /**
     * @param array<string, mixed> $payload
     */
    private function userCredentialsChanged(array $payload): bool
    {
        return isset($payload['password']);
    }

    private function updateLastUpdatedPasswordTimestamp(string $userId): void
    {
        $this->connection->update('user', [
            'last_updated_password_at' => (new \DateTime())->format(Defaults::STORAGE_DATE_TIME_FORMAT),
        ], [
            'id' => Uuid::fromHexToBytes($userId),
        ]);
    }
}
