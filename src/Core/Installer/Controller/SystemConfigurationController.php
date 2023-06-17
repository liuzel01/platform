<?php declare(strict_types=1);

namespace Shuwei\Core\Installer\Controller;

use Doctrine\DBAL\Connection;
use Shuwei\Core\Framework\Log\Package;
use Shuwei\Core\Installer\Configuration\AdminConfigurationService;
use Shuwei\Core\Installer\Configuration\EnvConfigWriter;
use Shuwei\Core\Installer\Configuration\SystemConfigurationService;
use Shuwei\Core\Installer\Database\BlueGreenDeploymentService;
use Shuwei\Core\Maintenance\System\Service\DatabaseConnectionFactory;
use Shuwei\Core\Maintenance\System\Struct\DatabaseConnectionInformation;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @internal
 *
 * @phpstan-type System array{name: string, locale: string, currency: string, additionalCurrencies: null|list<string>, country: string, email: string, host: string, basePath: string, schema: string, blueGreenDeployment: bool}
 * @phpstan-type AdminUser array{email: string, username: string, name: string, password: string}
 */
#[Package('core')]
class SystemConfigurationController extends InstallerController
{
    /**
     * @param array<string, string> $supportedLanguages
     * @param list<string> $supportedCurrencies
     */
    public function __construct(
        private readonly DatabaseConnectionFactory  $connectionFactory,
        private readonly EnvConfigWriter            $envConfigWriter,
        private readonly SystemConfigurationService $shopConfigurationService,
        private readonly AdminConfigurationService  $adminConfigurationService,
        private readonly TranslatorInterface        $translator,
        private readonly array                      $supportedLanguages,
        private readonly array                      $supportedCurrencies
    ) {
    }

    #[Route(path: '/installer/configuration', name: 'installer.configuration', methods: ['GET', 'POST'])]
    public function shopConfiguration(Request $request): Response
    {
        $session = $request->getSession();
        /** @var DatabaseConnectionInformation|null $connectionInfo */
        $connectionInfo = $session->get(DatabaseConnectionInformation::class);

        if (!$connectionInfo) {
            return $this->redirectToRoute('installer.database-configuration');
        }

        $connection = $this->connectionFactory->getConnection($connectionInfo);

        $error = null;

        if ($request->getMethod() === 'POST') {
            $adminUser = [
                'email' => (string) $request->request->get('config_admin_email'),
                'username' => (string) $request->request->get('config_admin_username'),
                'firstName' => (string) $request->request->get('config_admin_firstName'),
                'lastName' => (string) $request->request->get('config_admin_lastName'),
                'password' => (string) $request->request->get('config_admin_password'),
                'locale' => $this->supportedLanguages[$request->attributes->get('_locale')],
            ];

            /** @var list<string>|null $availableCurrencies */
            $availableCurrencies = $request->request->all('available_currencies');

            $schema = 'http';
            // This is for supporting Apache 2.2
            if (\array_key_exists('HTTPS', $_SERVER) && mb_strtolower((string) $_SERVER['HTTPS']) === 'on') {
                $schema = 'https';
            }
            if (\array_key_exists('REQUEST_SCHEME', $_SERVER)) {
                $schema = $_SERVER['REQUEST_SCHEME'];
            }

            $shop = [
                'name' => (string) $request->request->get('config_shopName'),
                'locale' => (string) $request->request->get('config_shop_language'),
                'currency' => (string) $request->request->get('config_shop_currency'),
                'additionalCurrencies' => $availableCurrencies ?: null,
                'country' => (string) $request->request->get('config_shop_country'),
                'email' => (string) $request->request->get('config_mail'),
                'host' => (string) $_SERVER['HTTP_HOST'],
                'schema' => $schema,
                'basePath' => str_replace('/index.php', '', (string) $_SERVER['SCRIPT_NAME']),
                'blueGreenDeployment' => (bool) $session->get(BlueGreenDeploymentService::ENV_NAME),
            ];

            try {
                $this->envConfigWriter->writeConfig($connectionInfo, $shop);

                $this->shopConfigurationService->updateShop($shop, $connection);
                $this->adminConfigurationService->createAdmin($adminUser, $connection);

                $session->remove(DatabaseConnectionInformation::class);
                $session->set('ADMIN_USER', $adminUser);

                return $this->redirectToRoute('installer.finish');
            } catch (\Exception $e) {
                $error = $e->getMessage();
            }
        }

        if (!$request->request->has('config_system_language')) {
            $request->request->set('config_system_language', $this->supportedLanguages[$request->attributes->get('_locale')]);
        }

        return $this->renderInstaller(
            '@Installer/installer/system-configuration.html.twig',
            [
                'error' => $error,
                'languageIsos' => $this->supportedLanguages,
                'parameters' => $request->request->all(),
            ]
        );
    }
}
