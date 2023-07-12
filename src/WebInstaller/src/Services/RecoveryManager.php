<?php
declare(strict_types=1);

namespace Shuwei\WebInstaller\Services;

use Shuwei\Core\Framework\Log\Package;
use Shuwei\WebInstaller\InstallerException;
use Symfony\Component\HttpFoundation\Request;

/**
 * @internal
 */
#[Package('core')]
class RecoveryManager
{
    public function getBinary(): string
    {
        /** @var string $fileName */
        $fileName = $_SERVER['SCRIPT_FILENAME'];

        return $fileName;
    }

    public function getPHPBinary(Request $request): string
    {
        $phpBinary = $request->getSession()->get('phpBinary');
        \assert(\is_string($phpBinary));

        return $phpBinary;
    }

    public function getProjectDir(): string
    {
        $fileName = realpath($_SERVER['SCRIPT_FILENAME']);
        \assert(\is_string($fileName));

        return \dirname($fileName);
    }

    public function getShuweiLocation(): string
    {
        $projectDir = $this->getProjectDir();

        $composerLookup = \dirname($projectDir) . '/composer.lock';

        // The Shuwei installation is always in the "public" directory
        if (basename($projectDir) !== 'public') {
            throw InstallerException::cannotFindShuweiInstallation();
        }

        if (file_exists($composerLookup)) {
            /** @var array{packages: array{name: string, version: string}[]} $composerJson */
            $composerJson = json_decode((string) file_get_contents($composerLookup), true, \JSON_THROW_ON_ERROR);

            foreach ($composerJson['packages'] as $package) {
                if ($package['name'] === '58shuwei/core' || $package['name'] === '58shuwei/platform') {
                    return \dirname($composerLookup);
                }
            }
        }

        throw InstallerException::cannotFindShuweiInstallation();
    }

    public function getCurrentShuweiVersion(string $shuweiPath): string
    {
        $lockFile = $shuweiPath . '/composer.lock';

        if (!file_exists($lockFile)) {
            throw InstallerException::cannotFindComposerLock();
        }

        /** @var array{packages: array{name: string, version: string}[]} $composerLock */
        $composerLock = json_decode((string) file_get_contents($lockFile), true, \JSON_THROW_ON_ERROR);

        foreach ($composerLock['packages'] as $package) {
            if ($package['name'] === '58shuwei/core' || $package['name'] === '58shuwei/platform') {
                return ltrim($package['version'], 'v');
            }
        }

        throw InstallerException::cannotFindShuweiInComposerLock();
    }

    public function isFlexProject(string $shuweiPath): bool
    {
        return file_exists($shuweiPath . '/symfony.lock');
    }
}
