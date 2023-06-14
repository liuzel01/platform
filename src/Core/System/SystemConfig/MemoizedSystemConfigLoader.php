<?php declare(strict_types=1);

namespace Shuwei\Core\System\SystemConfig;

use Shuwei\Core\Framework\Log\Package;
use Shuwei\Core\System\SystemConfig\Store\MemoizedSystemConfigStore;

#[Package('system-settings')]
class MemoizedSystemConfigLoader extends AbstractSystemConfigLoader
{
    /**
     * @internal
     */
    public function __construct(
        private readonly AbstractSystemConfigLoader $decorated,
        private readonly MemoizedSystemConfigStore $memoizedSystemConfigStore
    ) {
    }

    public function getDecorated(): AbstractSystemConfigLoader
    {
        return $this->decorated;
    }

    public function load(): array
    {
        $config = $this->memoizedSystemConfigStore->getConfig();

        if ($config !== null) {
            return $config;
        }

        $config = $this->getDecorated()->load();
        $this->memoizedSystemConfigStore->setConfig($config);

        return $config;
    }
}
