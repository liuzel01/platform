<?php declare(strict_types=1);

namespace Shuwei\Core\Content\Media\Aggregate\MediaThumbnail;

use Shuwei\Core\Framework\DataAbstractionLayer\EntityCollection;
use Shuwei\Core\Framework\Log\Package;

/**
 * @extends EntityCollection<MediaThumbnailEntity>
 */
#[Package('content')]
class MediaThumbnailCollection extends EntityCollection
{
    public function getApiAlias(): string
    {
        return 'media_thumbnail_collection';
    }

    protected function getExpectedClass(): string
    {
        return MediaThumbnailEntity::class;
    }
}
