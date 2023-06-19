<?php declare(strict_types=1);

namespace Shuwei\Core\Installer\Configuration;

use Doctrine\DBAL\Connection;
use Shuwei\Core\Framework\Log\Package;
use Shuwei\Core\Installer\Controller\SystemConfigurationController;
use Shuwei\Core\Maintenance\User\Service\UserProvisioner;

/**
 * @internal
 *
 * @phpstan-import-type AdminUser from SystemConfigurationController
 */
#[Package('core')]
class AdminConfigurationService
{
    /**
     * @param AdminUser $user
     */
    public function createAdmin(array $user, Connection $connection): void
    {
        $userProvisioner = new UserProvisioner($connection);
        $userProvisioner->provision(
            $user['username'],
            $user['password'],
            [
                'name' => $user['name'],
                'email' => $user['email'],
            ]
        );
    }
}
