<?php declare(strict_types=1);

namespace Shuwei\Core\Framework\Adapter\Cache;

use Doctrine\DBAL\Connection;
use Shuwei\Core\Framework\Adapter\Translation\Translator;
use Shuwei\Core\Framework\DataAbstractionLayer\Event\EntityWrittenContainerEvent;
use Shuwei\Core\Framework\Log\Package;
use Shuwei\Core\System\Snippet\SnippetDefinition;
use Shuwei\Core\System\SystemConfig\CachedSystemConfigLoader;


#[Package('core')]
class CacheInvalidationSubscriber
{
    /**
     * @internal
     */
    public function __construct(
        private readonly CacheInvalidator $cacheInvalidator,
        private readonly Connection $connection,
        private readonly bool $fineGrainedCacheSnippet,
        private readonly bool $fineGrainedCacheConfig
    ) {
    }



    public function invalidateConfig(): void
    {
        // invalidates the complete cached config
        $this->cacheInvalidator->invalidate([
            CachedSystemConfigLoader::CACHE_TAG,
        ]);
    }
    

    public function invalidateSnippets(EntityWrittenContainerEvent $event): void
    {
        if (!$this->fineGrainedCacheSnippet) {
            $this->cacheInvalidator->invalidate(['shuwei.translator']);

            return;
        }

        // invalidates all http cache items where the snippets used
        $snippets = $event->getEventByEntityName(SnippetDefinition::ENTITY_NAME);

        if (!$snippets) {
            return;
        }

        $tags = [];
        foreach ($snippets->getPayloads() as $payload) {
            if (isset($payload['translationKey'])) {
                $tags[] = Translator::buildName($payload['translationKey']);
            }
        }
        $this->cacheInvalidator->invalidate($tags);
    }
}
