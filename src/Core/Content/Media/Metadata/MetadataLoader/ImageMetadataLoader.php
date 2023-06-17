<?php declare(strict_types=1);

namespace Shuwei\Core\Content\Media\Metadata\MetadataLoader;

use Shuwei\Core\Content\Media\MediaType\ImageType;
use Shuwei\Core\Content\Media\MediaType\MediaType;
use Shuwei\Core\Framework\Log\Package;

#[Package('content')]
class ImageMetadataLoader implements MetadataLoaderInterface
{
    /**
     * @internal
     */
    public function __construct()
    {
    }

    /**
     * @return array{width: int, height: int, type: int}|null
     */
    public function extractMetadata(string $filePath): ?array
    {
        $metadata = \getimagesize($filePath);
        if (\is_array($metadata)) {
            return [
                'width' => $metadata[0],
                'height' => $metadata[1],
                'type' => $metadata[2],
            ];
        }

        return null;
    }

    public function supports(MediaType $mediaType): bool
    {
        return $mediaType instanceof ImageType;
    }
}
