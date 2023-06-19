<?php declare(strict_types=1);

namespace Shuwei\Core\Content\Media\Event;

use Shuwei\Core\Framework\Log\Package;
use Symfony\Contracts\EventDispatcher\Event;

#[Package('content')]
class MediaFileExtensionWhitelistEvent extends Event
{
    public function __construct(private array $whitelist)
    {
    }

    public function getWhitelist()
    {
        return $this->whitelist;
    }

    public function setWhitelist(array $whitelist): void
    {
        $this->whitelist = $whitelist;
    }
}
