<?php declare(strict_types=1);

namespace Shuwei\Core\Content\Media\TypeDetector;

use Shuwei\Core\Content\Media\File\MediaFile;
use Shuwei\Core\Content\Media\MediaType\AudioType;
use Shuwei\Core\Content\Media\MediaType\MediaType;
use Shuwei\Core\Framework\Log\Package;

#[Package('content')]
class AudioTypeDetector implements TypeDetectorInterface
{
    protected const SUPPORTED_FILE_EXTENSIONS = [
        'aac' => [],
        'flac' => [],
        'mp3' => [],
        'oga' => [],
        'wav' => [],
        'wma' => [],
    ];

    public function detect(MediaFile $mediaFile, ?MediaType $previouslyDetectedType): ?MediaType
    {
        $fileExtension = mb_strtolower($mediaFile->getFileExtension());
        if (!\array_key_exists($fileExtension, self::SUPPORTED_FILE_EXTENSIONS)) {
            return $previouslyDetectedType;
        }

        if ($previouslyDetectedType === null) {
            $previouslyDetectedType = new AudioType();
        }

        $previouslyDetectedType->addFlags(self::SUPPORTED_FILE_EXTENSIONS[$fileExtension]);

        return $previouslyDetectedType;
    }
}
