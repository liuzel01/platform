<?php declare(strict_types=1);

namespace Shuwei\Core\Framework\DataAbstractionLayer\Command;

use Shuwei\Core\Framework\Adapter\Console\ShuweiStyle;
use Shuwei\Core\Framework\DataAbstractionLayer\DefinitionInstanceRegistry;
use Shuwei\Core\Framework\DataAbstractionLayer\SchemaGenerator;
use Shuwei\Core\Framework\Log\Package;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'dal:create:schema',
    description: 'Creates the database schema',
)]
#[Package('core')]
class CreateSchemaCommand extends Command
{
    private readonly string $dir;

    /**
     * @internal
     */
    public function __construct(
        private readonly SchemaGenerator $schemaGenerator,
        private readonly DefinitionInstanceRegistry $registry,
        string $rootDir
    ) {
        parent::__construct();
        $this->dir = $rootDir . '/schema/';
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new ShuweiStyle($input, $output);
        $io->title('DAL generate schema');

        $entities = $this->registry->getDefinitions();
        $schema = [];

        foreach ($entities as $entity) {
            $domain = explode('_', $entity->getEntityName());
            $domain = array_shift($domain);
            $schema[$domain][] = $this->schemaGenerator->generate($entity);
        }

        $io->success('Created schema in ' . $this->dir);

        if (!file_exists($this->dir)) {
            mkdir($this->dir);
        }

        foreach ($schema as $domain => $sql) {
            file_put_contents($this->dir . '/' . $domain . '.sql', implode("\n\n", $sql));
        }

        return self::SUCCESS;
    }
}
