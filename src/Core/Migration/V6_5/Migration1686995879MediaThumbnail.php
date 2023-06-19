<?php declare(strict_types=1);

namespace Shuwei\Core\Migration\V6_5;

use Doctrine\DBAL\Connection;
use Shuwei\Core\Framework\Migration\MigrationStep;

class Migration1686995879MediaThumbnail extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1686995879;
    }

    public function update(Connection $connection): void
    {
        $connection->executeStatement('
            CREATE TABLE `media_thumbnail` (
              `id` BINARY(16) NOT NULL,
              `media_id` BINARY(16) NOT NULL,
              `width` INT(10) unsigned NOT NULL,
              `height` INT(10) unsigned NOT NULL,
              `custom_fields` JSON NULL,
              `created_at` DATETIME(3) NOT NULL,
              `updated_at` DATETIME(3) NULL,
               PRIMARY KEY (`id`),
               CONSTRAINT `json.media_thumbnail.custom_fields` CHECK (JSON_VALID(`custom_fields`)),
               CONSTRAINT `fk.media_thumbnail.media_id` FOREIGN KEY (`media_id`)
                 REFERENCES `media` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ');
    }

    public function updateDestructive(Connection $connection): void
    {
        // implement update destructive
    }
}
