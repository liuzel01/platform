<?php declare(strict_types=1);

namespace Shuwei\Core\Framework\Plugin\Command;

use Composer\IO\ConsoleIO;
use Shuwei\Core\Framework\Adapter\Cache\CacheClearer;
use Shuwei\Core\Framework\Adapter\Console\ShuweiStyle;
use Shuwei\Core\Framework\Context;
use Shuwei\Core\Framework\Log\Package;
use Shuwei\Core\Framework\Plugin\Exception\NoPluginFoundInZipException;
use Shuwei\Core\Framework\Plugin\PluginManagementService;
use Shuwei\Core\Framework\Plugin\PluginService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\HelperSet;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'plugin:zip-import',
    description: 'Imports a plugin from a zip file',
)]
#[Package('core')]
class PluginZipImportCommand extends Command
{
    /**
     * @internal
     */
    public function __construct(
        private readonly PluginManagementService $pluginManagementService,
        private readonly PluginService $pluginService,
        protected CacheClearer $cacheClearer
    ) {
        parent::__construct();
    }

    /**
     * {@inheritdoc}
     */
    protected function configure(): void
    {
        $this->addArgument('zip-file', InputArgument::REQUIRED, 'Zip file that contains a shuwei platform plugin.')
            ->addOption('no-refresh', null, InputOption::VALUE_OPTIONAL, 'Do not refresh plugin list.')
            ->addOption('delete', null, InputOption::VALUE_OPTIONAL, 'Delete the zip file after importing successfully.');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $zipFile = $input->getArgument('zip-file');
        $io = new ShuweiStyle($input, $output);
        $io->title('Shuwei Plugin Zip Import');

        try {
            $type = $this->pluginManagementService->extractPluginZip($zipFile, (bool) $input->getOption('delete'));
        } catch (NoPluginFoundInZipException $e) {
            $io->error($e->getMessage());

            return self::FAILURE;
        }

        if ($type === PluginManagementService::PLUGIN) {
            $this->cacheClearer->clearContainerCache();
        }

        $io->success('Successfully import zip file ' . basename((string) $zipFile));

        if (!$input->getOption('no-refresh')) {
            $composerInput = clone $input;
            $composerInput->setInteractive(false);
            $helperSet = $this->getHelperSet();
            \assert($helperSet instanceof HelperSet);

            $this->pluginService->refreshPlugins(
                Context::createDefaultContext(),
                new ConsoleIO($composerInput, $output, $helperSet)
            );
            $io->success('Plugin list refreshed');
        }

        return self::SUCCESS;
    }
}
