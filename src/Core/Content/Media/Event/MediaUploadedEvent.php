<?php declare(strict_types=1);

namespace Shuwei\Core\Content\Media\Event;

use Shuwei\Core\Framework\Context;
use Shuwei\Core\Framework\Event\EventData\EventDataCollection;
use Shuwei\Core\Framework\Event\EventData\ScalarValueType;
use Shuwei\Core\Framework\Event\FlowEventAware;
use Shuwei\Core\Framework\Log\Package;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * @deprecated tag:v6.6.0 - reason:class-hierarchy-change - MediaUploadedAware is deprecated and will be removed in v6.6.0
 */
#[Package('content')]
class MediaUploadedEvent extends Event implements FlowEventAware
{
    public const EVENT_NAME = 'media.uploaded';

    public function __construct(
        private readonly string $mediaId,
        private readonly Context $context
    ) {
    }

    public function getName(): string
    {
        return self::EVENT_NAME;
    }

    public static function getAvailableData(): EventDataCollection
    {
        return (new EventDataCollection())
            ->add('mediaId', new ScalarValueType(ScalarValueType::TYPE_STRING));
    }

    public function getValues(): array
    {
        return [

        ];
    }

    public function getMediaId(): string
    {
        return $this->mediaId;
    }

    public function getContext(): Context
    {
        return $this->context;
    }
}
