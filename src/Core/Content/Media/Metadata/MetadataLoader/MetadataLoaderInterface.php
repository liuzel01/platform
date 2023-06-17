<?php declare(strict_types=1);

namespace Shuwei\Core\Content\Media\Metadata\MetadataLoader;

use Shuwei\Core\Content\Media\MediaType\MediaType;
use Shuwei\Core\Framework\Log\Package;

#[Package('content')]
interface MetadataLoaderInterface
{
    /**
     * @return array<string, mixed>|null
     */
    public function extractMetadata(string $filePath): ?array;

    public function supports(MediaType $mediaType): bool;
}
