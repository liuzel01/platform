<?php declare(strict_types=1);

namespace Shuwei\Core\Framework\Struct;

use Shuwei\Core\Framework\Log\Package;

#[Package('core')]
trait VariablesAccessTrait
{
    public function getVars(): array
    {
        return get_object_vars($this);
    }
}
