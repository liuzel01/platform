<?php declare(strict_types=1);

namespace Shuwei\Core\Framework\Plugin\Context;

use Shuwei\Core\Framework\Context;
use Shuwei\Core\Framework\Log\Package;
use Shuwei\Core\Framework\Migration\MigrationCollection;
use Shuwei\Core\Framework\Plugin;

#[Package('core')]
class InstallContext
{
    private bool $autoMigrate = true;

    public function __construct(
        private readonly Plugin $plugin,
        private readonly Context $context,
        private readonly string $currentShuweiVersion,
        private readonly string $currentPluginVersion,
        private readonly MigrationCollection $migrationCollection
    ) {
    }

    public function getPlugin(): Plugin
    {
        return $this->plugin;
    }

    public function getContext(): Context
    {
        return $this->context;
    }

    public function getCurrentShuweiVersion(): string
    {
        return $this->currentShuweiVersion;
    }

    public function getCurrentPluginVersion(): string
    {
        return $this->currentPluginVersion;
    }

    public function getMigrationCollection(): MigrationCollection
    {
        return $this->migrationCollection;
    }

    public function isAutoMigrate(): bool
    {
        return $this->autoMigrate;
    }

    public function setAutoMigrate(bool $autoMigrate): void
    {
        $this->autoMigrate = $autoMigrate;
    }
}
