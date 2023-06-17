<?php declare(strict_types=1);

namespace Shuwei\Core\Content\Media\TypeDetector;

use Shuwei\Core\Content\Media\File\MediaFile;
use Shuwei\Core\Content\Media\MediaType\MediaType;
use Shuwei\Core\Framework\Log\Package;

#[Package('content')]
interface TypeDetectorInterface
{
    public function detect(MediaFile $mediaFile, ?MediaType $previouslyDetectedType): ?MediaType;
}
