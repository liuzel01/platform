<?php declare(strict_types=1);

namespace Shuwei\Core\Migration\V6_5;

use Doctrine\DBAL\Connection;
use Shuwei\Core\Framework\Migration\MigrationStep;

class Migration1686708279SnippetSet extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1686708279;
    }

    public function update(Connection $connection): void
    {
        $connection->executeStatement('
            CREATE TABLE IF NOT EXISTS `snippet_set` (
                `id`            BINARY(16)                              NOT NULL,
                `name`          VARCHAR(255) COLLATE utf8mb4_unicode_ci NOT NULL,
                `base_file`     VARCHAR(255) COLLATE utf8mb4_unicode_ci NOT NULL,
                `iso`           VARCHAR(255) COLLATE utf8mb4_unicode_ci NOT NULL,
                `custom_fields` JSON                                    NULL,
                `created_at`    DATETIME(3)                             NOT NULL,
                `updated_at`    DATETIME(3)                             NULL,
                PRIMARY KEY (`id`),
                CONSTRAINT `json.snippet_set.custom_fields` CHECK (JSON_VALID(`custom_fields`))
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ');
    }

    public function updateDestructive(Connection $connection): void
    {
        // implement update destructive
    }
}
