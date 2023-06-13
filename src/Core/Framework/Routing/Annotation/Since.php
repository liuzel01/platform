<?php declare(strict_types=1);

namespace Shuwei\Core\Framework\Routing\Annotation;

use Doctrine\Common\Annotations\Annotation\NamedArgumentConstructor;
use Shuwei\Core\Framework\Log\Package;

/**
 * @Annotation
 *
 * @NamedArgumentConstructor
 */
#[Package('core')]
class Since
{
    public function __construct(public string $version)
    {
    }
}
