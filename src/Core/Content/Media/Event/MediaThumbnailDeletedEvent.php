<?php declare(strict_types=1);

namespace Shuwei\Core\Content\Media\Event;

use Shuwei\Core\Content\Media\Aggregate\MediaThumbnail\MediaThumbnailCollection;
use Shuwei\Core\Framework\Context;
use Shuwei\Core\Framework\Log\Package;
use Symfony\Contracts\EventDispatcher\Event;

#[Package('content')]
class MediaThumbnailDeletedEvent extends Event
{
    final public const EVENT_NAME = 'media_thumbnail.after_delete';

    public function __construct(
        private readonly MediaThumbnailCollection $thumbnails,
        private readonly Context $context
    ) {
    }

    public function getThumbnails(): MediaThumbnailCollection
    {
        return $this->thumbnails;
    }

    public function getContext(): Context
    {
        return $this->context;
    }
}
