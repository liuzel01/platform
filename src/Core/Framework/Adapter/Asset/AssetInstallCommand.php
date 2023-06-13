<?php declare(strict_types=1);

namespace Shuwei\Core\Framework\Adapter\Asset;

use Shuwei\Core\Framework\Adapter\Console\ShuweiStyle;
use Shuwei\Core\Framework\Log\Package;
use Shuwei\Core\Framework\Plugin\Util\AssetService;
use Shuwei\Core\Installer\Installer;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpKernel\KernelInterface;

#[AsCommand(
    name: 'assets:install',
    description: 'Installs bundles web assets under a public web directory',
)]
#[Package('core')]
class AssetInstallCommand extends Command
{
    /**
     * @internal
     */
    public function __construct(
        private readonly KernelInterface $kernel,
        private readonly AssetService $assetService,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new ShuweiStyle($input, $output);

        foreach ($this->kernel->getBundles() as $bundle) {
            $io->writeln(sprintf('Copying files for bundle: %s', $bundle->getName()));
            $this->assetService->copyAssetsFromBundle($bundle->getName());
        }

        $io->writeln('Copying files for bundle: Installer');
        $this->assetService->copyAssets(new Installer());

        $publicDir = $this->kernel->getProjectDir() . '/public/';

        if (!file_exists($publicDir . '/.htaccess') && file_exists($publicDir . '/.htaccess.dist')) {
            $io->writeln('Copying .htaccess.dist to .htaccess');
            copy($publicDir . '/.htaccess.dist', $publicDir . '/.htaccess');
        }

        $io->success('Successfully copied all bundle files');

        return self::SUCCESS;
    }
}
