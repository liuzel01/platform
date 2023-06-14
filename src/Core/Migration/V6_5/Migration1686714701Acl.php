<?php declare(strict_types=1);

namespace Shuwei\Core\Migration\V6_5;

use Doctrine\DBAL\Connection;
use Shuwei\Core\Framework\Migration\MigrationStep;

class Migration1686714701Acl extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1686714701;
    }

    public function update(Connection $connection): void
    {
        $connection->executeStatement('
            CREATE TABLE `acl_role` (
              `id` binary(16) NOT NULL,
              `name` varchar(255) NOT NULL,
              `description` longtext DEFAULT NULL,
              `privileges` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`privileges`)),
              `created_at` datetime(3) NOT NULL,
              `updated_at` datetime(3) DEFAULT NULL,
              `deleted_at` datetime(3) DEFAULT NULL,
              PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ');
        $connection->executeStatement('
            CREATE TABLE `acl_user_role` (
              `user_id` binary(16) NOT NULL,
              `acl_role_id` binary(16) NOT NULL,
              `created_at` datetime(3) NOT NULL,
              `updated_at` datetime(3) DEFAULT NULL,
              PRIMARY KEY (`user_id`,`acl_role_id`),
              KEY `fk.acl_user_role.acl_role_id` (`acl_role_id`),
              CONSTRAINT `fk.acl_user_role.acl_role_id` FOREIGN KEY (`acl_role_id`) REFERENCES `acl_role` (`id`) ON DELETE CASCADE,
              CONSTRAINT `fk.acl_user_role.user_id` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ');
        $connection->executeStatement('ALTER TABLE `user` ADD `admin` tinyint(1) NULL AFTER `active`');

        $connection->executeStatement('UPDATE `user` SET `admin` = 1');
    }

    public function updateDestructive(Connection $connection): void
    {
        // implement update destructive
    }
}
