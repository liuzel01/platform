<?php declare(strict_types=1);

namespace Shuwei\Core\Framework\Update\Event;

use Shuwei\Core\Framework\Context;
use Shuwei\Core\Framework\Log\Package;
use Symfony\Contracts\EventDispatcher\Event;

#[Package('system-settings')]
abstract class UpdateEvent extends Event
{
    public function __construct(private readonly Context $context)
    {
    }

    public function getContext(): Context
    {
        return $this->context;
    }
}
