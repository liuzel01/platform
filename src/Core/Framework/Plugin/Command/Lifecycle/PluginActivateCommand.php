<?php declare(strict_types=1);

namespace Shuwei\Core\Framework\Plugin\Command\Lifecycle;

use Shuwei\Core\Framework\Adapter\Console\ShuweiStyle;
use Shuwei\Core\Framework\Context;
use Shuwei\Core\Framework\Log\Package;
use Shuwei\Core\Framework\Plugin\Exception\PluginNotInstalledException;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'plugin:activate',
    description: 'Activate a plugin',
)]
#[Package('core')]
class PluginActivateCommand extends AbstractPluginLifecycleCommand
{
    private const LIFECYCLE_METHOD = 'activate';

    protected function configure(): void
    {
        $this->configureCommand(self::LIFECYCLE_METHOD);
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

        $activatedPluginCount = 0;
        foreach ($plugins as $plugin) {
            if ($plugin->getInstalledAt() === null) {
                $io->note(sprintf('Plugin "%s" must be installed. Skipping.', $plugin->getName()));

                continue;
            }

            if ($plugin->getActive()) {
                $io->note(sprintf('Plugin "%s" is already active. Skipping.', $plugin->getName()));

                continue;
            }

            $this->pluginLifecycleService->activatePlugin($plugin, $context);
            ++$activatedPluginCount;

            $io->text(sprintf('Plugin "%s" has been activated successfully.', $plugin->getName()));
        }

        if ($activatedPluginCount !== 0) {
            $io->success(sprintf('Activated %d plugin(s).', $activatedPluginCount));
        }

        $this->handleClearCacheOption($input, $io, 'activating');

        return self::SUCCESS;
    }
}
