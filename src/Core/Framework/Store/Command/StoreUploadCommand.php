<?php declare(strict_types=1);

namespace Shuwei\Core\Framework\Store\Command;

use Shuwei\Core\Framework\Log\Package;
use Shuwei\Core\Framework\Store\Services\StoreClient;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @internal
 */
#[AsCommand(
    name: 'store:upload',
    description: 'Upload a plugin to the store',
)]
#[Package('merchant-services')]
class StoreUploadCommand extends Command
{
    public function __construct(
        private readonly StoreClient $storeClient,
    )
    {
        parent::__construct();
    }
    protected function configure(): void
    {
        $this->addOption('path', 'p', InputOption::VALUE_OPTIONAL, 'Path of plugin')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        return self::SUCCESS;
    }
}
