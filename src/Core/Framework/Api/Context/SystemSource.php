<?php declare(strict_types=1);

namespace Shuwei\Core\Framework\Api\Context;

use Shuwei\Core\Framework\Log\Package;

#[Package('core')]
class SystemSource implements ContextSource
{
    public string $type = 'system';
}
