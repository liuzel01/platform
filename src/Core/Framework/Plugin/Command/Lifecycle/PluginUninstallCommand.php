<?php declare(strict_types=1);

namespace Shuwei\Core\Framework\Plugin\Command\Lifecycle;

use Shuwei\Core\Framework\Adapter\Console\ShuweiStyle;
use Shuwei\Core\Framework\Context;
use Shuwei\Core\Framework\Log\Package;
use Shuwei\Core\Framework\Plugin\Exception\PluginNotInstalledException;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'plugin:uninstall',
    description: 'Uninstall a plugin',
)]
#[Package('core')]
class PluginUninstallCommand extends AbstractPluginLifecycleCommand
{
    private const LIFECYCLE_METHOD = 'uninstall';

    protected function configure(): void
    {
        $this->configureCommand(self::LIFECYCLE_METHOD);
        $this->addOption('keep-user-data', null, InputOption::VALUE_NONE, 'Keep user data of the plugin');
    }

    /**
     * {@inheritdoc}
     *
     * @throws PluginNotInstalledException
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new ShuweiStyle($input, $output);
        $context = Context::createDefaultContext();
        $plugins = $this->prepareExecution(self::LIFECYCLE_METHOD, $io, $input, $context);

        if ($plugins === null) {
            return self::SUCCESS;
        }

        $keepUserData = $input->getOption('keep-user-data');

        $uninstalledPluginCount = 0;
        foreach ($plugins as $plugin) {
            if ($plugin->getInstalledAt() === null) {
                $io->note(sprintf('Plugin "%s" is not installed. Skipping.', $plugin->getName()));

                continue;
            }

            $this->pluginLifecycleService->uninstallPlugin($plugin, $context, $keepUserData);
            ++$uninstalledPluginCount;

            $io->text(sprintf('Plugin "%s" has been uninstalled successfully.', $plugin->getName()));
        }

        if ($uninstalledPluginCount !== 0) {
            $io->success(sprintf('Uninstalled %d plugins.', $uninstalledPluginCount));
        }

        $this->handleClearCacheOption($input, $io, 'uninstalling');

        return self::SUCCESS;
    }
}
