<?php declare(strict_types=1);

namespace Shuwei\Core\Content\Media\Pathname\PathnameStrategy;

use Shuwei\Core\Content\Media\Aggregate\MediaThumbnail\MediaThumbnailEntity;
use Shuwei\Core\Content\Media\MediaEntity;
use Shuwei\Core\Framework\Log\Package;

#[Package('content')]
class IdPathnameStrategy extends AbstractPathNameStrategy
{
    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return 'id';
    }

    /**
     * {@inheritdoc}
     */
    public function generatePathHash(MediaEntity $media, ?MediaThumbnailEntity $thumbnail = null): ?string
    {
        return $this->generateMd5Path($media->getId());
    }
}
