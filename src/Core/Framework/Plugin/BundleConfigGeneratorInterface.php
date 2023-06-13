<?php declare(strict_types=1);

namespace Shuwei\Core\Framework\Plugin;

use Shuwei\Core\Framework\Log\Package;

#[Package('core')]
interface BundleConfigGeneratorInterface
{
    /**
     * Returns the bundle config for the webpack plugin injector
     */
    public function getConfig(): array;
}
