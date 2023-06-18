<?php declare(strict_types=1);

namespace Shuwei\Core\Framework\Test\Cache;

use PHPUnit\Framework\TestCase;
use Shuwei\Core\Framework\Adapter\Cache\CacheIdLoader;
use Shuwei\Core\Framework\Adapter\Storage\AbstractKeyValueStorage;
use Shuwei\Core\Framework\Test\TestCaseBase\IntegrationTestBehaviour;
use Shuwei\Core\Framework\Uuid\Uuid;

/**
 * @internal
 *
 * @group cache
 */
class CacheIdLoaderTest extends TestCase
{
    use IntegrationTestBehaviour;

    private CacheIdLoader $loader;

    protected function setUp(): void
    {
        parent::setUp();
        $this->loader = $this->getContainer()->get(CacheIdLoader::class);
    }

    public function testLoadExisting(): void
    {
        $id = Uuid::randomHex();

        $storage = $this->createMock(AbstractKeyValueStorage::class);
        $storage->method('get')->willReturn($id);

        $loader = new CacheIdLoader($storage);

        static::assertSame($id, $loader->load());
    }

    public function testMissingCacheIdWritesId(): void
    {
        $storage = $this->createMock(AbstractKeyValueStorage::class);
        $storage->method('get')->willReturn(false);

        $loader = new CacheIdLoader($storage);

        static::assertTrue(Uuid::isValid($loader->load()));
    }

    public function testCacheIdIsNotAString(): void
    {
        $storage = $this->createMock(AbstractKeyValueStorage::class);
        $storage->method('get')->willReturn(0);

        $loader = new CacheIdLoader($storage);

        static::assertTrue(Uuid::isValid($loader->load()));
    }

    public function testCacheIdIsLoadedFromDatabase(): void
    {
        $old = $this->loader->load();

        static::assertTrue(Uuid::isValid($old));

        $new = Uuid::randomHex();

        $this->getContainer()->get(AbstractKeyValueStorage::class)->set('cache-id', $new);

        static::assertSame($new, $this->loader->load());

        $this->loader->write($old);

        static::assertSame($old, $this->loader->load());
    }
}
