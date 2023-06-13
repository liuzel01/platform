<?php declare(strict_types=1);

namespace Shuwei\Core\Framework\Adapter\Cache\Message;

use Shuwei\Core\Framework\Adapter\Cache\CacheClearer;
use Shuwei\Core\Framework\Log\Package;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

/**
 * @internal
 */
#[AsMessageHandler]
#[Package('core')]
final class CleanupOldCacheFoldersHandler
{
    public function __construct(private readonly CacheClearer $cacheClearer)
    {
    }

    public function __invoke(CleanupOldCacheFolders $message): void
    {
        $this->cacheClearer->cleanupOldContainerCacheDirectories();
    }
}
