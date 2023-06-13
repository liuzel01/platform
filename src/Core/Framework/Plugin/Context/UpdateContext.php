<?php declare(strict_types=1);

namespace Shuwei\Core\Framework\Plugin\Context;

use Shuwei\Core\Framework\Context;
use Shuwei\Core\Framework\Log\Package;
use Shuwei\Core\Framework\Migration\MigrationCollection;
use Shuwei\Core\Framework\Plugin;

#[Package('core')]
class UpdateContext extends InstallContext
{
    public function __construct(
        Plugin $plugin,
        Context $context,
        string $currentShuweiVersion,
        string $currentPluginVersion,
        MigrationCollection $migrationCollection,
        private readonly string $updatePluginVersion
    ) {
        parent::__construct($plugin, $context, $currentShuweiVersion, $currentPluginVersion, $migrationCollection);
    }

    public function getUpdatePluginVersion(): string
    {
        return $this->updatePluginVersion;
    }
}
