<?php declare(strict_types=1);

namespace Shuwei\Core\Content\Media\Pathname\PathnameStrategy;

use Shuwei\Core\Content\Media\Aggregate\MediaThumbnail\MediaThumbnailEntity;
use Shuwei\Core\Content\Media\MediaEntity;
use Shuwei\Core\Framework\Log\Package;

#[Package('content')]
abstract class AbstractPathNameStrategy implements PathnameStrategyInterface
{
    private array $blacklist = [
        'ad' => 'g0',
    ];

    /**
     * {@inheritdoc}
     */
    public function generatePhysicalFilename(MediaEntity $media, ?MediaThumbnailEntity $thumbnail = null): string
    {
        $filenameSuffix = '';
        if ($thumbnail !== null) {
            $filenameSuffix = sprintf('_%dx%d', $thumbnail->getWidth(), $thumbnail->getHeight());
        }

        $extension = $media->getFileExtension() ? '.' . $media->getFileExtension() : '';

        return $media->getFileName() . $filenameSuffix . $extension;
    }

    /**
     * {@inheritdoc}
     */
    public function generatePathCacheBuster(MediaEntity $media, ?MediaThumbnailEntity $thumbnail = null): ?string
    {
        $uploadedAt = $media->getUploadedAt();

        if ($uploadedAt === null) {
            return null;
        }

        return (string) $uploadedAt->getTimestamp();
    }

    protected function generateMd5Path(string $fromValue): string
    {
        $md5hash = md5($fromValue);

        $md5hashSlices = \array_slice(str_split($md5hash, 2), 0, 3);
        $md5hashSlices = array_map(
            fn ($slice) => \array_key_exists($slice, $this->blacklist) ? $this->blacklist[$slice] : $slice,
            $md5hashSlices
        );

        return implode('/', $md5hashSlices);
    }
}
