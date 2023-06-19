<?php declare(strict_types=1);

namespace Shuwei\Core\Maintenance\System\Command;

use Shuwei\Core\Framework\Adapter\Cache\CacheClearer;
use Shuwei\Core\Framework\Adapter\Console\ShuweiStyle;
use Shuwei\Core\Framework\Log\Package;
use Shuwei\Core\Maintenance\System\Service\SystemConfigurator;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @internal should be used over the CLI only
 */
#[AsCommand(
    name: 'system:configure-system',
    description: 'Configure system',
)]
#[Package('core')]
class SystemConfigureCommand extends Command
{
    public function __construct(
        private readonly SystemConfigurator $systemConfigurator,
        private readonly CacheClearer       $cacheClearer
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {

    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output = new ShuweiStyle($input, $output);


        $this->cacheClearer->clear();

        return (int) Command::SUCCESS;
    }
}
