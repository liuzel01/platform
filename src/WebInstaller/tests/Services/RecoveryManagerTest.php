<?php
declare(strict_types=1);

namespace Shuwei\WebInstaller\Tests\Services;

use PHPUnit\Framework\TestCase;
use Shuwei\WebInstaller\Services\RecoveryManager;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;

/**
 * @internal
 *
 * @covers \Shuwei\WebInstaller\Services\RecoveryManager
 */
class RecoveryManagerTest extends TestCase
{
    public function testGetBinary(): void
    {
        $recoveryManager = new RecoveryManager();

        static::assertSame($_SERVER['SCRIPT_FILENAME'], $recoveryManager->getBinary());
    }

    public function testGetProjectDir(): void
    {
        $recoveryManager = new RecoveryManager();

        $fileName = realpath($_SERVER['SCRIPT_FILENAME']);
        static::assertIsString($fileName);
        static::assertSame(\dirname($fileName), $recoveryManager->getProjectDir());
    }

    public function testGetShuweiLocationReturnsFalseMissingShuwei(): void
    {
        $recoveryManager = new RecoveryManager();

        static::expectException(\RuntimeException::class);
        static::expectExceptionMessage('Could not find Shuwei installation');
        $recoveryManager->getShuweiLocation();
    }

    /**
     * @backupGlobals enabled
     */
    public function testGetShuweiLocationFailsDueNonPublicDirectory(): void
    {
        $recoveryManager = new RecoveryManager();

        $fs = new Filesystem();
        $tmpDir = sys_get_temp_dir() . '/' . uniqid('shuwei', true);

        $_SERVER['SCRIPT_FILENAME'] = $tmpDir . '/foo/shuwei-installer.phar.php';
        $fs->mkdir($tmpDir . '/foo');
        $fs->touch($tmpDir . '/foo/shuwei-installer.phar.php');

        static::expectException(\RuntimeException::class);
        static::expectExceptionMessage('Could not find Shuwei installation');
        $recoveryManager->getShuweiLocation();

        $fs->remove($tmpDir);
    }

    /**
     * @backupGlobals enabled
     */
    public function testGetShuweiLocationReturnsShuweiLocation(): void
    {
        $recoveryManager = new RecoveryManager();

        $tmpDir = sys_get_temp_dir() . '/' . uniqid('shuwei', true);

        $fs = new Filesystem();
        $this->prepareShuwei($fs, $tmpDir);

        static::assertSame(realpath($tmpDir), $recoveryManager->getShuweiLocation());

        $fs->remove($tmpDir);
    }

    /**
     * @backupGlobals enabled
     */
    public function testGetShuweiVersion(): void
    {
        $recoveryManager = new RecoveryManager();

        $tmpDir = sys_get_temp_dir() . '/' . uniqid('shuwei', true);

        $fs = new Filesystem();
        $this->prepareShuwei($fs, $tmpDir);

        static::assertSame('6.4.10.0', $recoveryManager->getCurrentShuweiVersion($tmpDir));

        $fs->remove($tmpDir);
    }

    /**
     * @backupGlobals enabled
     */
    public function testGetShuweiVersionPrefixed(): void
    {
        $recoveryManager = new RecoveryManager();

        $tmpDir = sys_get_temp_dir() . '/' . uniqid('shuwei', true);

        $fs = new Filesystem();
        $this->prepareShuwei($fs, $tmpDir, 'v6.4.10.0');

        static::assertSame('6.4.10.0', $recoveryManager->getCurrentShuweiVersion($tmpDir));

        $fs->remove($tmpDir);
    }

    public function testGetVersionEmptyFolder(): void
    {
        $recoveryManager = new RecoveryManager();

        static::expectException(\RuntimeException::class);
        static::expectExceptionMessage('Could not find composer.lock file');
        $recoveryManager->getCurrentShuweiVersion(__DIR__);
    }

    public function testGetVersionFromLock(): void
    {
        $recoveryManager = new RecoveryManager();

        $tmpDir = sys_get_temp_dir() . '/' . uniqid('shuwei', true);

        $fs = new Filesystem();

        $fs->mkdir($tmpDir);

        $fs->dumpFile($tmpDir . '/composer.lock', json_encode([
            'packages' => [],
        ], \JSON_THROW_ON_ERROR));

        static::expectException(\RuntimeException::class);
        static::expectExceptionMessage('Could not find Shuwei in composer.lock file');
        $recoveryManager->getCurrentShuweiVersion($tmpDir);
    }

    public function testSymfonyLock(): void
    {
        $recoveryManager = new RecoveryManager();

        static::assertFalse($recoveryManager->isFlexProject(__DIR__));

        $tmpDir = sys_get_temp_dir() . '/' . uniqid('shuwei', true);

        $fs = new Filesystem();

        $fs->mkdir($tmpDir);
        $fs->dumpFile($tmpDir . '/symfony.lock', json_encode([], \JSON_THROW_ON_ERROR));

        static::assertTrue($recoveryManager->isFlexProject($tmpDir));
    }

    public function testGetPHPBinary(): void
    {
        $recoveryManager = new RecoveryManager();

        $request = new Request();
        $request->setSession(new Session(new MockArraySessionStorage()));
        $request->getSession()->set('phpBinary', 'php');

        static::assertSame('php', $recoveryManager->getPHPBinary($request));
    }

    private function prepareShuwei(Filesystem $fs, string $tmpDir, string $version = '6.4.10.0'): void
    {
        $fs->mkdir($tmpDir);
        $fs->mkdir($tmpDir . '/public');

        $_SERVER['SCRIPT_FILENAME'] = $tmpDir . '/public/shuwei-installer.phar.php';
        $fs->touch($_SERVER['SCRIPT_FILENAME']);

        $fs->dumpFile($tmpDir . '/composer.json', json_encode([
            'require' => [
                'shuwei/core' => $version,
            ],
        ], \JSON_THROW_ON_ERROR));

        $fs->dumpFile($tmpDir . '/composer.lock', json_encode([
            'packages' => [
                [
                    'name' => 'shuwei/core',
                    'version' => $version,
                ],
            ],
        ], \JSON_THROW_ON_ERROR));
    }
}
