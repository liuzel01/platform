<?php declare(strict_types=1);

namespace Shuwei\Core\Migration\V6_5;

use Doctrine\DBAL\Connection;
use Shuwei\Core\Framework\Migration\MigrationStep;

class Migration1686995867MediaFolderConfigurationMediaThumbnailSize extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1686995867;
    }

    public function update(Connection $connection): void
    {
        $connection->executeStatement('
            CREATE TABLE `media_folder_configuration_media_thumbnail_size` (
                `media_folder_configuration_id` BINARY(16) NOT NULL,
                `media_thumbnail_size_id` BINARY(16) NOT NULL,
                PRIMARY KEY (`media_folder_configuration_id`, `media_thumbnail_size_id`),
                CONSTRAINT `fk.media_folder_configuration_media_thumbnail_size.conf_id` FOREIGN KEY (`media_folder_configuration_id`)
                  REFERENCES `media_folder_configuration` (`id`) ON DELETE CASCADE,
                CONSTRAINT `fk.media_folder_configuration_media_thumbnail_size.size_id` FOREIGN KEY (`media_thumbnail_size_id`)
                  REFERENCES `media_thumbnail_size` (`id`) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ');
    }

    public function updateDestructive(Connection $connection): void
    {
        // implement update destructive
    }
}
