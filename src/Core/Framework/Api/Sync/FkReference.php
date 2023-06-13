<?php declare(strict_types=1);

namespace Shuwei\Core\Framework\Api\Sync;

use Shuwei\Core\Framework\Log\Package;

/**
 * @final
 */
#[Package('core')]
class FkReference
{
    public ?string $resolved = null;

    public function __construct(public mixed $value)
    {
    }
}
