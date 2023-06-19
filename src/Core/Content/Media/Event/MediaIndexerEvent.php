<?php declare(strict_types=1);

namespace Shuwei\Core\Content\Media\Event;

use Shuwei\Core\Framework\Context;
use Shuwei\Core\Framework\Event\NestedEvent;
use Shuwei\Core\Framework\Log\Package;

#[Package('content')]
class MediaIndexerEvent extends NestedEvent
{
    public function __construct(
        private readonly array $ids,
        private readonly Context $context,
        private readonly array $skip = []
    ) {
    }

    public function getContext(): Context
    {
        return $this->context;
    }

    public function getIds(): array
    {
        return $this->ids;
    }

    public function getSkip(): array
    {
        return $this->skip;
    }
}
