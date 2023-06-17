<?php declare(strict_types=1);

namespace Shuwei\Core\Content\Media\Aggregate\MediaTranslation;

use Shuwei\Core\Content\Media\MediaEntity;
use Shuwei\Core\Framework\DataAbstractionLayer\EntityCustomFieldsTrait;
use Shuwei\Core\Framework\DataAbstractionLayer\TranslationEntity;
use Shuwei\Core\Framework\Log\Package;

#[Package('content')]
class MediaTranslationEntity extends TranslationEntity
{
    use EntityCustomFieldsTrait;

    /**
     * @var string
     */
    protected $mediaId;

    /**
     * @var string|null
     */
    protected $title;

    /**
     * @var string|null
     */
    protected $alt;

    /**
     * @var MediaEntity|null
     */
    protected $media;

    public function getMediaId(): string
    {
        return $this->mediaId;
    }

    public function setMediaId(string $mediaId): void
    {
        $this->mediaId = $mediaId;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): void
    {
        $this->title = $title;
    }

    public function getAlt(): ?string
    {
        return $this->alt;
    }

    public function setAlt(?string $alt): void
    {
        $this->alt = $alt;
    }

    public function getMedia(): ?MediaEntity
    {
        return $this->media;
    }

    public function setMedia(MediaEntity $media): void
    {
        $this->media = $media;
    }
}
