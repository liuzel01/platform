<?php declare(strict_types=1);

namespace Shuwei\Core\Migration\V6_5;

use Doctrine\DBAL\Connection;
use Shuwei\Core\Framework\Migration\MigrationStep;

class Migration1686991921MediaDefaultFolder extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1686991921;
    }

    public function update(Connection $connection): void
    {
        $connection->executeStatement('
            CREATE TABLE `media_default_folder` (
              `id`                  BINARY(16)      NOT NULL,
              `association_fields`  JSON            NOT NULL,
              `entity`              VARCHAR(255)    NOT NULL,
              `custom_fields`       JSON            NULL,
              `created_at`          DATETIME(3)     NOT NULL,
              `updated_at`          DATETIME(3)     NULL,
              PRIMARY KEY (`id`),
              UNIQUE KEY `uniq.media_default_folder.entity` (`entity`),
              CONSTRAINT `json.media_default_folder.custom_fields` CHECK (JSON_VALID(`custom_fields`)),
              CONSTRAINT `json.media_default_folder.association_fields` CHECK (JSON_VALID(`association_fields`))
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ');
    }

    public function updateDestructive(Connection $connection): void
    {
        // implement update destructive
    }
}
