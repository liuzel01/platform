<?php declare(strict_types=1);

namespace Shuwei\Core\Content\Media\Pathname;

use Shuwei\Core\Content\Media\Aggregate\MediaThumbnail\MediaThumbnailEntity;
use Shuwei\Core\Content\Media\MediaEntity;
use Shuwei\Core\Framework\Log\Package;

#[Package('content')]
interface UrlGeneratorInterface
{
    public function getAbsoluteMediaUrl(MediaEntity $media): string;

    public function getRelativeMediaUrl(MediaEntity $media): string;

    public function getAbsoluteThumbnailUrl(MediaEntity $media, MediaThumbnailEntity $thumbnail): string;

    public function getRelativeThumbnailUrl(MediaEntity $media, MediaThumbnailEntity $thumbnail): string;
}
