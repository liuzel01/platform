<?php declare(strict_types=1);

namespace Shuwei\Core\Test\Annotation;

use Shuwei\Core\Framework\Log\Package;

/**
 * @internal
 *
 * @Annotation
 *
 * @Target({"METHOD", "CLASS"})
 */
#[Package('core')]
final class DisabledFeatures
{
    /**
     * @var array<string>
     */
    public array $features;
}
