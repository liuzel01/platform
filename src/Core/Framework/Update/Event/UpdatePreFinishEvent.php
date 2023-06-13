<?php declare(strict_types=1);

namespace Shuwei\Core\Framework\Update\Event;

use Shuwei\Core\Framework\Context;
use Shuwei\Core\Framework\Log\Package;

#[Package('system-settings')]
class UpdatePreFinishEvent extends UpdateEvent
{
    public function __construct(
        Context $context,
        private readonly string $oldVersion,
        private readonly string $newVersion
    ) {
        parent::__construct($context);
    }

    public function getOldVersion(): string
    {
        return $this->oldVersion;
    }

    public function getNewVersion(): string
    {
        return $this->newVersion;
    }
}
