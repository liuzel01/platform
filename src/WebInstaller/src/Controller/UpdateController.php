<?php
declare(strict_types=1);

namespace Shuwei\WebInstaller\Controller;

use Shuwei\Core\Framework\Log\Package;
use Shuwei\WebInstaller\Services\FlexMigrator;
use Shuwei\WebInstaller\Services\ProjectComposerJsonUpdater;
use Shuwei\WebInstaller\Services\RecoveryManager;
use Shuwei\WebInstaller\Services\ReleaseInfoProvider;
use Shuwei\WebInstaller\Services\StreamedCommandResponseGenerator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @internal
 */
#[Package('core')]
class UpdateController extends AbstractController
{
    public function __construct(
        private readonly RecoveryManager $recoveryManager,
        private readonly ReleaseInfoProvider $releaseInfoProvider,
        private readonly FlexMigrator $flexMigrator,
        private readonly StreamedCommandResponseGenerator $streamedCommandResponseGenerator
    ) {
    }

    #[Route('/update', name: 'update', defaults: ['step' => 2], methods: ['GET'])]
    public function index(Request $request): Response
    {
        try {
            $shuweiPath = $this->recoveryManager->getShuweiLocation();
        } catch (\RuntimeException) {
            return $this->redirectToRoute('configure');
        }

        $currentShuweiVersion = $this->recoveryManager->getCurrentShuweiVersion($shuweiPath);
        $latestVersions = $this->getLatestVersions($request);

        if (empty($latestVersions)) {
            return $this->redirectToRoute('finish');
        }

        return $this->render('update.html.twig', [
            'shuweiPath' => $shuweiPath,
            'currentShuweiVersion' => $currentShuweiVersion,
            'isFlexProject' => $this->recoveryManager->isFlexProject($shuweiPath),
            'versions' => $latestVersions,
        ]);
    }

    #[Route('/update/_migrate-template', name: 'migrate-template', methods: ['POST'])]
    public function migrateTemplate(): Response
    {
        $shuweiPath = $this->recoveryManager->getShuweiLocation();

        $this->flexMigrator->cleanup($shuweiPath);
        $this->flexMigrator->patchRootComposerJson($shuweiPath);
        $this->flexMigrator->copyNewTemplateFiles($shuweiPath);
        $this->flexMigrator->migrateEnvFile($shuweiPath);

        return new Response('', Response::HTTP_NO_CONTENT);
    }

    #[Route('/update/_run', name: 'update_run', methods: ['POST'])]
    public function run(Request $request): Response
    {
        $version = $request->query->get('shuweiVersion', '');

        $shuweiPath = $this->recoveryManager->getShuweiLocation();

        ProjectComposerJsonUpdater::update(
            $shuweiPath . '/composer.json',
            $version
        );

        return $this->streamedCommandResponseGenerator->runJSON([
            $this->recoveryManager->getPhpBinary($request),
            '-dmemory_limit=1G',
            $this->recoveryManager->getBinary(),
            'composer',
            'update',
            '-d',
            $shuweiPath,
            '--no-interaction',
            '--no-ansi',
            '--no-scripts',
            '-v',
            '--with-all-dependencies', // update all packages
        ]);
    }

    #[Route('/update/_reset_config', name: 'update_reset_config', methods: ['POST'])]
    public function resetConfig(Request $request): Response
    {
        if (\function_exists('opcache_reset')) {
            opcache_reset();
        }

        $shuweiPath = $this->recoveryManager->getShuweiLocation();

        $this->patchSymfonyFlex($shuweiPath);

        return $this->streamedCommandResponseGenerator->runJSON([
            $this->recoveryManager->getPhpBinary($request),
            '-dmemory_limit=1G',
            $this->recoveryManager->getBinary(),
            'composer',
            '-d',
            $shuweiPath,
            'symfony:recipes:install',
            '--force',
            '--reset',
            '--no-interaction',
            '--no-ansi',
            '-v',
        ]);
    }

    #[Route('/update/_prepare', name: 'update_prepare', methods: ['POST'])]
    public function prepare(Request $request): Response
    {
        $shuweiPath = $this->recoveryManager->getShuweiLocation();

        return $this->streamedCommandResponseGenerator->runJSON([
            $this->recoveryManager->getPhpBinary($request),
            '-dmemory_limit=1G',
            $shuweiPath . '/bin/console',
            'system:update:prepare',
            '--no-interaction',
        ]);
    }

    #[Route('/update/_finish', name: 'update_finish', methods: ['POST'])]
    public function finish(Request $request): Response
    {
        $shuweiPath = $this->recoveryManager->getShuweiLocation();

        return $this->streamedCommandResponseGenerator->runJSON([
            $this->recoveryManager->getPhpBinary($request),
            '-dmemory_limit=1G',
            $shuweiPath . '/bin/console',
            'system:update:finish',
            '--no-interaction',
        ]);
    }

    /**
     * @see https://github.com/symfony/flex/pull/963
     */
    public function patchSymfonyFlex(string $shuweiPath): void
    {
        $optionsPhp = (string) file_get_contents($shuweiPath . '/vendor/symfony/flex/src/Options.php');

        $optionsPhp = str_replace(
            'return $this->io && $this->io->askConfirmation(sprintf(\'Cannot determine the state of the "%s" file, overwrite anyway? [y/N] \', $file), false);',
            'return $this->io && $this->io->askConfirmation(sprintf(\'Cannot determine the state of the "%s" file, overwrite anyway? [y/N] \', $file));',
            $optionsPhp
        );

        $optionsPhp = str_replace(
            'return $this->io && $this->io->askConfirmation(sprintf(\'File "%s" has uncommitted changes, overwrite? [y/N] \', $name), false);',
            'return $this->io && $this->io->askConfirmation(sprintf(\'File "%s" has uncommitted changes, overwrite? [y/N] \', $name));',
            $optionsPhp
        );

        file_put_contents($shuweiPath . '/vendor/symfony/flex/src/Options.php', $optionsPhp);
    }

    /**
     * @return array<string>
     */
    private function getLatestVersions(Request $request): array
    {
        if ($request->getSession()->has('latestVersions')) {
            $sessionValue = $request->getSession()->get('latestVersions');
            \assert(\is_array($sessionValue));

            return $sessionValue;
        }

        $shuweiPath = $this->recoveryManager->getShuweiLocation();

        $currentVersion = $this->recoveryManager->getCurrentShuweiVersion($shuweiPath);
        $latestVersions = $this->releaseInfoProvider->fetchUpdateVersions($currentVersion);

        $request->getSession()->set('latestVersions', $latestVersions);

        return $latestVersions;
    }
}
