<?php declare(strict_types=1);

namespace Shuwei\Core\Installer\Configuration;

use Defuse\Crypto\Exception\EnvironmentIsBrokenException;
use Defuse\Crypto\Key;
use Shuwei\Core\Framework\Log\Package;
use Shuwei\Core\Installer\Controller\SystemConfigurationController;
use Shuwei\Core\Installer\Finish\UniqueIdGenerator;
use Shuwei\Core\Maintenance\System\Struct\DatabaseConnectionInformation;

/**
 * @internal
 *
 * @phpstan-import-type System from SystemConfigurationController
 */
#[Package('core')]
class EnvConfigWriter
{
    private const FLEX_DOTENV = <<<'EOT'
###> symfony/lock ###
# Choose one of the stores below
# postgresql+advisory://db_user:db_password@localhost/db_name
LOCK_DSN=flock
###< symfony/lock ###

###> shuwei/core ###
APP_ENV=prod
APP_URL=http://127.0.0.1:8000
APP_SECRET=SECRET_PLACEHOLDER
INSTANCE_ID=INSTANCEID_PLACEHOLDER
BLUE_GREEN_DEPLOYMENT=0
DATABASE_URL=mysql://root:root@localhost/shuwei
###< shuwei/core ###
EOT;

    public function __construct(
        private readonly string $projectDir,
        private readonly UniqueIdGenerator $idGenerator
    ) {
    }

    /**
     * @param DatabaseConnectionInformation $info
     * @param array $system
     * @throws EnvironmentIsBrokenException
     */
    public function writeConfig(DatabaseConnectionInformation $info, array $system): void
    {
        $uniqueId = $this->idGenerator->getUniqueId();
        $secret = Key::createNewRandomKey()->saveToAsciiSafeString();

        // Copy flex default .env if missing
        if (!file_exists($this->projectDir . '/.env')) {
            $template = str_replace(
                [
                    'SECRET_PLACEHOLDER',
                    'INSTANCEID_PLACEHOLDER',
                ],
                [
                    $secret,
                    $uniqueId,
                ],
                self::FLEX_DOTENV
            );
            file_put_contents($this->projectDir . '/.env', $template);
        }

        $newEnv = [];

        $newEnv[] = 'APP_SECRET=' . $secret;
        $newEnv[] = 'APP_URL=' . $system['schema'] . '://' . $system['host'] . $system['basePath'];
        $newEnv[] = 'DATABASE_URL=' . $info->asDsn();

        if (!empty($info->getSslCaPath())) {
            $newEnv[] = 'DATABASE_SSL_CA=' . $info->getSslCaPath();
        }

        if (!empty($info->getSslCertPath())) {
            $newEnv[] = 'DATABASE_SSL_CERT=' . $info->getSslCertPath();
        }

        if (!empty($info->getSslCertKeyPath())) {
            $newEnv[] = 'DATABASE_SSL_KEY=' . $info->getSslCertKeyPath();
        }

        if ($info->getSslDontVerifyServerCert() !== null) {
            $newEnv[] = 'DATABASE_SSL_DONT_VERIFY_SERVER_CERT=' . ($info->getSslDontVerifyServerCert() ? '1' : '');
        }

        $newEnv[] = 'COMPOSER_HOME=' . $this->projectDir . '/var/cache/composer';
        $newEnv[] = 'INSTANCE_ID=' . $uniqueId;
        $newEnv[] = 'BLUE_GREEN_DEPLOYMENT=' . (int) $system['blueGreenDeployment'];
        $newEnv[] = 'OPENSEARCH_URL=http://localhost:9200';
        $newEnv[] = 'ADMIN_OPENSEARCH_URL=http://localhost:9200';

        file_put_contents($this->projectDir . '/.env.local', implode("\n", $newEnv));

        $htaccessPath = $this->projectDir . '/public/.htaccess';

        if (file_exists($htaccessPath . '.dist') && !file_exists($htaccessPath)) {
            $perms = fileperms($htaccessPath . '.dist');
            copy($htaccessPath . '.dist', $htaccessPath);

            if ($perms) {
                chmod($htaccessPath, $perms | 0644);
            }
        }
    }
}
