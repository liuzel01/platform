<?php declare(strict_types=1);

namespace Shuwei\Core\Content\Media\Pathname\PathnameStrategy;

use Shuwei\Core\Content\Media\Aggregate\MediaThumbnail\MediaThumbnailEntity;
use Shuwei\Core\Content\Media\MediaEntity;
use Shuwei\Core\Framework\Log\Package;

#[Package('content')]
class PhysicalFilenamePathnameStrategy extends AbstractPathNameStrategy
{
    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return 'physical_filename';
    }

    /**
     * {@inheritdoc}
     */
    public function generatePathHash(MediaEntity $media, ?MediaThumbnailEntity $thumbnail = null): ?string
    {
        $timestamp = $media->getUploadedAt() ? $media->getUploadedAt()->getTimestamp() . '/' : '';

        return $this->generateMd5Path($timestamp . $media->getFileName());
    }
}
