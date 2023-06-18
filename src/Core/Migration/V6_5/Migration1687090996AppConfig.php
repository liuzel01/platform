<?php declare(strict_types=1);

namespace Shuwei\Core\Migration\V6_5;

use Doctrine\DBAL\Connection;
use Shuwei\Core\Framework\Migration\MigrationStep;
use Shuwei\Core\Framework\Uuid\Uuid;

class Migration1687090996AppConfig extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1687090996;
    }

    public function update(Connection $connection): void
    {
        $connection->executeStatement('
            CREATE TABLE `app_config` (
              `key` varchar(50) NOT NULL,
              `value` LONGTEXT NOT NULL,
               PRIMARY KEY (`key`)
            );
        ');

        $connection->insert('app_config', [
            '`key`' => 'cache-id',
            '`value`' => Uuid::randomHex(),
        ]);
    }

    public function updateDestructive(Connection $connection): void
    {
        // implement update destructive
    }
}
