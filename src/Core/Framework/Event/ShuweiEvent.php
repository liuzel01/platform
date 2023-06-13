<?php declare(strict_types=1);

namespace Shuwei\Core\Framework\Event;

use Shuwei\Core\Framework\Context;
use Shuwei\Core\Framework\Log\Package;

#[Package('core')]
interface ShuweiEvent
{
    public function getContext(): Context;
}
