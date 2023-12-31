<?php declare(strict_types=1);

namespace Shuwei\Core\Framework\Plugin\Command;

use Shuwei\Core\Framework\Log\Package;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

#[AsCommand(
    name: 'plugin:create',
    description: 'Creates a new plugin',
)]
#[Package('core')]
class PluginCreateCommand extends Command
{
    private string $composerTemplate = <<<EOL
{
  "name": "swag/plugin-skeleton",
  "description": "Skeleton plugin",
  "type": "shuwei-platform-plugin",
  "license": "MIT",
  "autoload": {
    "psr-4": {
      "#namespace#\\\\": "src/"
    }
  },
  "extra": {
    "shuwei-plugin-class": "#namespace#\\\\#class#",
    "label": {
      "zh-CN": "插件结构",
      "en-US": "Skeleton plugin"
    }
  }
}

EOL;

    private string $bootstrapTemplate = <<<EOL
<?php declare(strict_types=1);

namespace #namespace#;

use Shuwei\Core\Framework\Plugin;

class #class# extends Plugin
{
}
EOL;

    private string $servicesXmlTemplate = <<<EOL
<?xml version="1.0" ?>

<container xmlns="http://symfony.com/schema/dic/services"
           xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
           xsi:schemaLocation="http://symfony.com/schema/dic/services http://symfony.com/schema/dic/services/services-1.0.xsd">

    <services>

    </services>
</container>
EOL;

    private string $configXmlTemplate = <<<EOL
<?xml version="1.0" encoding="UTF-8"?>
<config xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
        xsi:noNamespaceSchemaLocation="https://raw.githubusercontent.com/shuwei/platform/trunk/src/Core/System/SystemConfig/Schema/config.xsd">
    <card>
        <title>#pluginName# Settings</title>
        <title lang="en-US">#pluginName# Einstellungen</title>

        <input-field type="bool">
            <name>active</name>
            <label>Active</label>
            <label lang="en-US">Aktiviert</label>
        </input-field>
    </card>
</config>
EOL;

    /**
     * @internal
     */
    public function __construct(private readonly string $projectDir)
    {
        parent::__construct();
    }

    /**
     * {@inheritdoc}
     */
    protected function configure(): void
    {
        $this
            ->addArgument('name', InputArgument::OPTIONAL)
            ->addOption('create-config', 'c', InputOption::VALUE_NONE, 'Create config.xml');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $name = $input->getArgument('name');

        if (!$name) {
            $question = new Question('Please enter a plugin name: ');
            $name = $this->getHelper('question')->ask($input, $output, $question);
        }

        $name = ucfirst((string) $name);

        $directory = $this->projectDir . '/plugins/' . $name;

        if (file_exists($directory)) {
            throw new \RuntimeException(sprintf('Plugin directory %s already exists', $directory));
        }

        mkdir($directory . '/src/Resources/config/', 0777, true);

        $composerFile = $directory . '/composer.json';
        $bootstrapFile = $directory . '/src/' . $name . '.php';
        $servicesXmlFile = $directory . '/src/Resources/config/services.xml';

        $composer = str_replace(
            ['#namespace#', '#class#'],
            [$name, $name],
            $this->composerTemplate
        );

        $bootstrap = str_replace(
            ['#namespace#', '#class#'],
            [$name, $name],
            $this->bootstrapTemplate
        );

        file_put_contents($composerFile, $composer);
        file_put_contents($bootstrapFile, $bootstrap);
        file_put_contents($servicesXmlFile, $this->servicesXmlTemplate);

        if ($input->getOption('create-config')) {
            $configXmlFile = $directory . '/src/Resources/config/config.xml';
            $configXml = str_replace(
                ['pluginName'],
                [$name],
                $this->configXmlTemplate
            );

            file_put_contents($configXmlFile, $configXml);
        }

        return self::SUCCESS;
    }
}
