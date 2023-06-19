<?php declare(strict_types=1);

namespace Shuwei\Core\Installer\Configuration;

use Doctrine\DBAL\ArrayParameterType;
use Doctrine\DBAL\Connection;
use Shuwei\Core\Defaults;
use Shuwei\Core\Framework\Api\Util\AccessKeyHelper;
use Shuwei\Core\Framework\Log\Package;
use Shuwei\Core\Framework\Uuid\Uuid;
use Shuwei\Core\Installer\Controller\SystemConfigurationController;
use Shuwei\Core\Maintenance\System\Service\SystemConfigurator;

/**
 * @internal
 *
 * @codeCoverageIgnore - Is tested by integration test, does not make sense to unit test
 * as the sole purpose of this class is to configure the DB according to the configuration
 *
 * @phpstan-import-type Shop from SystemConfigurationController
 */
#[Package('core')]
class SystemConfigurationService
{

    public function updateShop(array $system, Connection $connection): void
    {
        if (empty($system['locale']) || empty($system['host'])) {
            throw new \RuntimeException('Please fill in all required fields. (system configuration)');
        }

        $systemConfigurator = new SystemConfigurator($connection);
        $systemConfigurator->updateBasicInformation($system['name'], $system['email']);
        $systemConfigurator->setDefaultLanguage($system['locale']);
    }

    private function getSnippetSet(string $iso, Connection $connection): ?string
    {
        return $connection->fetchOne(
            'SELECT id FROM snippet_set WHERE LOWER(iso) = LOWER(:iso)',
            ['iso' => $iso]
        ) ?: null;
    }
}
