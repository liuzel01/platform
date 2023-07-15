<?php declare(strict_types=1);

namespace Shuwei\Core\Installer\Database;

use Doctrine\DBAL\Connection;
use Psr\Log\NullLogger;
use Shuwei\Core\Framework\Log\Package;
use Shuwei\Core\Framework\Migration\MigrationCollectionLoader;
use Shuwei\Core\Framework\Migration\MigrationRuntime;
use Shuwei\Core\Framework\Migration\MigrationSource;

/**
 * @internal
 */
#[Package('core')]
class MigrationCollectionFactory
{
    public function __construct(private readonly string $projectDir)
    {
    }

    public function getMigrationCollectionLoader(Connection $connection): MigrationCollectionLoader
    {
        return new MigrationCollectionLoader(
            $connection,
            new MigrationRuntime($connection, new NullLogger()),
            $this->collect()
        );
    }

    /**
     * @return list<MigrationSource>
     */
    private function collect(): array
    {
        return [
            new MigrationSource('core', []),
            $this->createMigrationSource('V6_3'),
            $this->createMigrationSource('V6_4'),
            $this->createMigrationSource('V6_5'),
            $this->createMigrationSource('V6_6'),
        ];
    }

    private function createMigrationSource(string $version): MigrationSource
    {
        if (file_exists($this->projectDir . '/platform/src/Core/schema.sql')) {
            $coreBasePath = $this->projectDir . '/platform/src/Core';
            $frontendBasePath = $this->projectDir . '/platform/src/Frontend';
            $adminBasePath = $this->projectDir . '/platform/src/Administration';
        } elseif (file_exists($this->projectDir . '/src/Core/schema.sql')) {
            $coreBasePath = $this->projectDir . '/src/Core';
            $frontendBasePath = $this->projectDir . '/src/Frontend';
            $adminBasePath = $this->projectDir . '/src/Administration';
        } else {
            $coreBasePath = $this->projectDir . '/vendor/shuwei/core';
            $frontendBasePath = $this->projectDir . '/vendor/shuwei/frontend';
            $adminBasePath = $this->projectDir . '/vendor/shuwei/administration';
        }

        $hasFrontendMigrations = is_dir($frontendBasePath);
        $hasAdminMigrations = is_dir($adminBasePath);

        $source = new MigrationSource('core.' . $version, [
            sprintf('%s/Migration/%s', $coreBasePath, $version) => sprintf('Shuwei\\Core\\Migration\\%s', $version),
        ]);

        if ($hasFrontendMigrations) {
            $source->addDirectory(sprintf('%s/Migration/%s', $frontendBasePath, $version), sprintf('Shuwei\\Frontend\\Migration\\%s', $version));
        }

        if ($hasAdminMigrations) {
            $source->addDirectory(sprintf('%s/Migration/%s', $adminBasePath, $version), sprintf('Shuwei\\Administration\\Migration\\%s', $version));
        }

        return $source;
    }
}
