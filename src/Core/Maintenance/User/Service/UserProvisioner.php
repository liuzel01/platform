<?php declare(strict_types=1);

namespace Shuwei\Core\Maintenance\User\Service;

use Doctrine\DBAL\Connection;
use Shuwei\Core\Defaults;
use Shuwei\Core\Framework\DataAbstractionLayer\Field\PasswordField;
use Shuwei\Core\Framework\DataAbstractionLayer\FieldSerializer\PasswordFieldSerializer;
use Shuwei\Core\Framework\Log\Package;
use Shuwei\Core\Framework\Util\Random;
use Shuwei\Core\Framework\Uuid\Uuid;

#[Package('core')]
class UserProvisioner
{
    /**
     * @internal
     */
    public function __construct(private readonly Connection $connection)
    {
    }

    /**
     * @param array{name?: string, email?: string, localeId?: string, admin?: bool} $additionalData
     */
    public function provision(string $username, ?string $password = null, array $additionalData = []): string
    {
        if ($this->userExists($username)) {
            throw new \RuntimeException(sprintf('User with username "%s" already exists.', $username));
        }

        $minPasswordLength = $this->getAdminPasswordMinLength();

        if ($password && \strlen($password) < $minPasswordLength) {
            throw new \InvalidArgumentException(sprintf('The password length cannot be shorter than %d characters.', $minPasswordLength));
        }

        $password = $password ?? Random::getAlphanumericString(max($minPasswordLength, 8));

        $userPayload = [
            'id' => Uuid::randomBytes(),
            'name' => $additionalData['name'] ?? $username,
            'email' => $additionalData['email'] ?? 'info@shuwei.com',
            'username' => $username,
            'password' => password_hash($password, \PASSWORD_BCRYPT),
            'locale_id' => $additionalData['localeId'] ?? $this->getLocaleOfSystemLanguage(),
            'active' => true,
            'admin' => $additionalData['admin'] ?? true,
            'created_at' => (new \DateTime())->format(Defaults::STORAGE_DATE_TIME_FORMAT),
        ];

        $this->connection->insert('user', $userPayload);

        return $password;
    }

    private function userExists(string $username): bool
    {
        $builder = $this->connection->createQueryBuilder();

        return $builder->select('1')
            ->from('user')
            ->where('username = :username')
            ->setParameter('username', $username)
            ->executeQuery()
            ->rowCount() > 0;
    }

    private function getLocaleOfSystemLanguage(): string
    {
        $builder = $this->connection->createQueryBuilder();

        return (string) $builder->select('locale.id')
                ->from('language', 'language')
                ->innerJoin('language', 'locale', 'locale', 'language.locale_id = locale.id')
                ->where('language.id = :id')
                ->setParameter('id', Uuid::fromHexToBytes(Defaults::LANGUAGE_SYSTEM))
                ->executeQuery()
                ->fetchOne();
    }

    private function getAdminPasswordMinLength(): int
    {
        $configKey = PasswordFieldSerializer::CONFIG_MIN_LENGTH_FOR[PasswordField::FOR_ADMIN];

        $result = $this->connection->fetchOne(
            'SELECT configuration_value FROM system_config WHERE configuration_key = :configKey;',
            [
                'configKey' => $configKey,
            ]
        );

        if ($result === false) {
            return 0;
        }

        $config = json_decode($result, true);

        return $config['_value'] ?? 0;
    }
}
