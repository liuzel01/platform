<?php declare(strict_types=1);

namespace Shuwei\Core\Framework\Adapter\Cache;

use Shuwei\Core\Framework\Adapter\Translation\AbstractTranslator;
use Shuwei\Core\Framework\Log\Package;
use Shuwei\Core\Framework\Plugin\Exception\DecorationPatternException;
use Shuwei\Core\System\SystemConfig\SystemConfigService;

/**
 * @extends AbstractCacheTracer<mixed|null>
 */
#[Package('core')]
class CacheTracer extends AbstractCacheTracer
{
    /**
     * @internal
     */
    public function __construct(
        private readonly SystemConfigService $config,
        private readonly AbstractTranslator $translator,
        private readonly CacheTagCollection $collection
    ) {
    }

    public function getDecorated(): AbstractCacheTracer
    {
        throw new DecorationPatternException(self::class);
    }

    /**
     * @return mixed|null All kind of data could be cached
     */
    public function trace(string $key, \Closure $param)
    {
        return $this->collection->trace($key, fn () => $this->translator->trace($key, fn () => $this->config->trace($key, $param)));
    }

    public function get(string $key): array
    {
        return array_merge(
            $this->collection->getTrace($key),
            $this->config->getTrace($key),
            $this->translator->getTrace($key)
        );
    }
}
