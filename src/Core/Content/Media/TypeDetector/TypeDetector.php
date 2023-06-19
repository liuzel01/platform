<?php declare(strict_types=1);

namespace Shuwei\Core\Content\Media\TypeDetector;

use Shuwei\Core\Content\Media\File\MediaFile;
use Shuwei\Core\Content\Media\MediaType\MediaType;
use Shuwei\Core\Framework\Log\Package;
use Shuwei\Core\System\Annotation\Concept\ExtensionPattern\HandlerChain;

/**
 * @HandlerChain(
 *     serviceTag="shuwei.media_type.detector",
 *     handlerInterface="TypeDetectorInterface"
 * )
 */
#[Package('content')]
class TypeDetector implements TypeDetectorInterface
{
    /**
     * @internal
     *
     * @param TypeDetectorInterface[] $typeDetector
     */
    public function __construct(private readonly iterable $typeDetector)
    {
    }

    public function detect(MediaFile $mediaFile, ?MediaType $previouslyDetectedType = null): MediaType
    {
        $mediaType = null;
        foreach ($this->typeDetector as $typeDetector) {
            $mediaType = $typeDetector->detect($mediaFile, $mediaType);
        }

        return $mediaType;
    }
}
