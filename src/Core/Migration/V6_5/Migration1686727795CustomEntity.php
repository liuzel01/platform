<?php declare(strict_types=1);

namespace Shuwei\Core\Migration\V6_5;

use Doctrine\DBAL\Connection;
use Shuwei\Core\Framework\Migration\MigrationStep;

class Migration1686727795CustomEntity extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1686727795;
    }

    public function update(Connection $connection): void
    {
        $connection->executeStatement('
            CREATE TABLE IF NOT EXISTS `custom_entity` (
              `id` binary(16) NOT NULL,
              `name` varchar(255) NOT NULL,
              `fields` json NOT NULL,

              `created_at` DATETIME(3) NOT NULL,
              `updated_at` DATETIME(3) NULL,
              PRIMARY KEY (`id`),
              UNIQUE `name` (`name`),
              CONSTRAINT `json.custom_entity.fields` CHECK (JSON_VALID(`fields`))
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ');
    }

    public function updateDestructive(Connection $connection): void
    {
        // implement update destructive
    }
}
