<?php declare(strict_types=1);

namespace Shuwei\Core\Framework\Adapter\Database;

use Doctrine\DBAL\Connections\PrimaryReadReplicaConnection;
use Shuwei\Core\Framework\Log\Package;
use Shuwei\Core\Kernel;

/**
 * @internal
 */
#[Package('core')]
class ReplicaConnection
{
    public static function ensurePrimary(): void
    {
        $connection = Kernel::getConnection();

        if ($connection instanceof PrimaryReadReplicaConnection) {
            $connection->ensureConnectedToPrimary();
        }
    }
}
