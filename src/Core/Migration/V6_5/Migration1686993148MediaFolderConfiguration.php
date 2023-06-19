<?php declare(strict_types=1);

namespace Shuwei\Core\Migration\V6_5;

use Doctrine\DBAL\Connection;
use Shuwei\Core\Framework\Migration\MigrationStep;

class Migration1686993148MediaFolderConfiguration extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1686993148;
    }

    public function update(Connection $connection): void
    {
        $connection->executeStatement('
            CREATE TABLE `media_folder_configuration` (
              `id` binary(16) NOT NULL,
              `create_thumbnails` tinyint(1) DEFAULT 1,
              `thumbnail_quality` int(11) DEFAULT 80,
              `media_thumbnail_sizes_ro` longblob DEFAULT NULL,
              `keep_aspect_ratio` tinyint(1) DEFAULT 1,
              `private` tinyint(1) DEFAULT 0,
              `no_association` tinyint(1) DEFAULT NULL,
              `custom_fields` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`custom_fields`)),
              `created_at` datetime(3) NOT NULL,
              `updated_at` datetime(3) DEFAULT NULL,
              PRIMARY KEY (`id`),
              CONSTRAINT `json.media_folder_configuration.custom_fields` CHECK (json_valid(`custom_fields`))
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ');
    }

    public function updateDestructive(Connection $connection): void
    {
        // implement update destructive
    }
}
