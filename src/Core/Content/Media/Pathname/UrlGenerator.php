<?php declare(strict_types=1);

namespace Shuwei\Core\Content\Media\Pathname;

use League\Flysystem\FilesystemOperator;
use Shuwei\Core\Content\Media\Aggregate\MediaThumbnail\MediaThumbnailEntity;
use Shuwei\Core\Content\Media\Exception\EmptyMediaFilenameException;
use Shuwei\Core\Content\Media\Exception\EmptyMediaIdException;
use Shuwei\Core\Content\Media\MediaEntity;
use Shuwei\Core\Content\Media\Pathname\PathnameStrategy\PathnameStrategyInterface;
use Shuwei\Core\Framework\Log\Package;
use Symfony\Contracts\Service\ResetInterface;

#[Package('content')]
class UrlGenerator implements UrlGeneratorInterface, ResetInterface
{
    /**
     * @internal
     */
    public function __construct(
        private readonly PathnameStrategyInterface $pathnameStrategy,
        private readonly FilesystemOperator $filesystem
    ) {
    }

    /**
     * @throws EmptyMediaFilenameException
     * @throws EmptyMediaIdException
     */
    public function getRelativeMediaUrl(MediaEntity $media): string
    {
        $this->validateMedia($media);

        return $this->toPathString([
            'media',
            $this->pathnameStrategy->generatePathHash($media),
            $this->pathnameStrategy->generatePathCacheBuster($media),
            $this->pathnameStrategy->generatePhysicalFilename($media),
        ]);
    }

    /**
     * @throws EmptyMediaFilenameException
     * @throws EmptyMediaIdException
     */
    public function getAbsoluteMediaUrl(MediaEntity $media): string
    {
        return $this->filesystem->publicUrl($this->getRelativeMediaUrl($media));
    }

    /**
     * @throws EmptyMediaFilenameException
     * @throws EmptyMediaIdException
     */
    public function getRelativeThumbnailUrl(MediaEntity $media, MediaThumbnailEntity $thumbnail): string
    {
        $this->validateMedia($media);

        return $this->toPathString([
            'thumbnail',
            $this->pathnameStrategy->generatePathHash($media),
            $this->pathnameStrategy->generatePathCacheBuster($media),
            $this->pathnameStrategy->generatePhysicalFilename($media, $thumbnail),
        ]);
    }

    /**
     * @throws EmptyMediaFilenameException
     * @throws EmptyMediaIdException
     */
    public function getAbsoluteThumbnailUrl(MediaEntity $media, MediaThumbnailEntity $thumbnail): string
    {
        return $this->filesystem->publicUrl($this->getRelativeThumbnailUrl($media, $thumbnail));
    }

    public function reset(): void
    {
    }

    private function toPathString(array $parts): string
    {
        return implode('/', array_filter($parts));
    }

    /**
     * @throws EmptyMediaFilenameException
     * @throws EmptyMediaIdException
     */
    private function validateMedia(MediaEntity $media): void
    {
        if (empty($media->getId())) {
            throw new EmptyMediaIdException();
        }

        if (empty($media->getFileName())) {
            throw new EmptyMediaFilenameException();
        }
    }
}
