<?php
declare(strict_types=1);

namespace Shuwei\WebInstaller\Services;

use Shuwei\Core\Framework\Log\Package;
use Symfony\Component\Filesystem\Filesystem;

/**
 * @internal
 *
 * @phpstan-type ComposerRepository array{type: string, url: string, options: array{symlink: bool}}
 */
#[Package('core')]
class FlexMigrator
{
    private const REMOVE_FILES = [
        '.dockerignore',
        'Dockerfile',
        'PLATFORM_COMMIT_SHA',
        'README.md',
        'config/services.xml',
        'config/services_test.xml',
        'easy-coding-standard.php',
        'license.txt',
        'phpstan.neon',
        'phpunit.xml.dist',
        'psalm.xml',
        'public/index.php',
        'src/TestBootstrap.php',
        'var/plugins.json',
    ];

    private const REMOVE_DIRECTORIES = [
        '.github',
        '.gitlab-ci',
        'gitlab-ci.yml',
        'bin',
        'config/etc',
        'config/services',
        'public/recovery',
        'files/update',
    ];

    private const ENV_DEFAULT = <<<EOT
###> symfony/lock ###
# Choose one of the stores below
# postgresql+advisory://db_user:db_password@localhost/db_name
LOCK_DSN=flock
###< symfony/lock ###
###> symfony/messenger ###
# Choose one of the transports below
# MESSENGER_TRANSPORT_DSN=doctrine://default
# MESSENGER_TRANSPORT_DSN=amqp://guest:guest@localhost:5672/%2f/messages
# MESSENGER_TRANSPORT_DSN=redis://localhost:6379/messages
###< symfony/messenger ###
###> symfony/mailer ###
# MAILER_DSN=null://null
###< symfony/mailer ###
###> shuwei/core ###
APP_ENV=prod
APP_URL=http://127.0.0.1:8000
APP_SECRET=7628a40c75b25f8a1f14b3812c3b250b
INSTANCE_ID=41322ddd2b70bd29ef65d402f025c785
BLUE_GREEN_DEPLOYMENT=0
DATABASE_URL=mysql://root:root@localhost/shuwei
# With Shuwei 6.4.17.0 the MAILER_DSN variable will be used in this template instead of MAILER_URL
MAILER_URL=null://null
###< shuwei/core ###

EOT;

    /**
     * Delete all files and directories that are not needed anymore
     */
    public function cleanup(string $projectDir): void
    {
        $fs = new Filesystem();

        foreach (self::REMOVE_FILES as $file) {
            $path = $projectDir . '/' . $file;
            if ($fs->exists($path)) {
                $fs->remove($path);
            }
        }

        foreach (self::REMOVE_DIRECTORIES as $directory) {
            $path = $projectDir . '/' . $directory;

            if ($fs->exists($path)) {
                $fs->remove($path);
            }
        }

        $fs->mkdir($projectDir . '/bin');
    }

    public function patchRootComposerJson(string $projectDir): void
    {
        $composerJsonPath = $projectDir . '/composer.json';

        /** @var array{require: array<string, string>, config?: array{platform?: string, "allow-plugins": array<string, bool>, repositories?: ComposerRepository[]}} $composerJson */
        $composerJson = json_decode((string) file_get_contents($composerJsonPath), true, \JSON_THROW_ON_ERROR);

        $composerJson['require']['symfony/flex'] = '^2';
        $composerJson['require']['symfony/runtime'] = '^5.0|^6.0';

        // Remove old recovery
        unset($composerJson['require']['58shuwei/recovery']);

        if (!isset($composerJson['config'])) {
            $composerJson['config'] = [];
        }

        if (!isset($composerJson['config']['allow-plugins'])) {
            $composerJson['config']['allow-plugins'] = [];
        }

        $composerJson['config']['allow-plugins']['symfony/flex'] = true;
        $composerJson['config']['allow-plugins']['symfony/runtime'] = true;

        unset($composerJson['config']['platform']);

        $composerJson['scripts'] = [
            'auto-scripts' => [
                'assets:install' => 'symfony-cmd',
            ],
            'post-install-cmd' => [
                '@auto-scripts',
            ],
            'post-update-cmd' => [
                '@auto-scripts',
            ],
        ];

        $composerJson['extra']['symfony'] = [
            'allow-contrib' => true,
            'endpoint' => [
                'https://raw.githubusercontent.com/58shuwei/recipes/flex/main/index.json',
                'flex://defaults',
            ],
        ];

        $composerJson['require-dev'] = [
            'shuwei/dev-tools' => '*',
        ];

        if (!isset($composerJson['repositories'])) {
            $composerJson['repositories'] = [];
        }

        $composerJson['repositories'] = $this->addSymlinkRepository($composerJson['repositories']);

        file_put_contents($composerJsonPath, json_encode($composerJson, \JSON_THROW_ON_ERROR | \JSON_PRETTY_PRINT | \JSON_UNESCAPED_SLASHES));
    }

    public function copyNewTemplateFiles(string $projectDir): void
    {
        $fs = new Filesystem();

        $fs->mirror(__DIR__ . '/../Resources/flex-config/', $projectDir);
    }

    public function migrateEnvFile(string $projectDir): void
    {
        $envPath = $projectDir . '/.env';

        if (!file_exists($envPath)) {
            file_put_contents($envPath, self::ENV_DEFAULT);

            return;
        }

        rename($envPath, $envPath . '.local');
        file_put_contents($envPath, self::ENV_DEFAULT);
    }

    /**
     * @param ComposerRepository[] $repositories
     *
     * @return ComposerRepository[]
     */
    private function addSymlinkRepository(array $repositories): array
    {
        $existingRepos = array_column($repositories, 'url');

        if (!\in_array('plugins/*', $existingRepos, true)) {
            $repositories[] = [
                'type' => 'path',
                'url' => 'plugins/*',
                'options' => [
                    'symlink' => true,
                ],
            ];
        }

        if (!\in_array('plugins/*/packages/*', $existingRepos, true)) {
            $repositories[] = [
                'type' => 'path',
                'url' => 'plugins/*/packages/*',
                'options' => [
                    'symlink' => true,
                ],
            ];
        }

        return $repositories;
    }
}
