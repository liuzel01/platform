<?php declare(strict_types=1);

namespace Shuwei\Core\Content\Media;

use Shuwei\Core\Content\Media\Aggregate\MediaFolder\MediaFolderEntity;
use Shuwei\Core\Content\Media\Aggregate\MediaThumbnail\MediaThumbnailCollection;
use Shuwei\Core\Content\Media\Aggregate\MediaTranslation\MediaTranslationCollection;
use Shuwei\Core\Content\Media\MediaType\MediaType;
use Shuwei\Core\Framework\DataAbstractionLayer\Entity;
use Shuwei\Core\Framework\DataAbstractionLayer\EntityCustomFieldsTrait;
use Shuwei\Core\Framework\DataAbstractionLayer\EntityIdTrait;
use Shuwei\Core\Framework\Log\Package;
use Shuwei\Core\System\User\UserCollection;
use Shuwei\Core\System\User\UserEntity;

#[Package('content')]
class MediaEntity extends Entity
{
    use EntityCustomFieldsTrait;
    use EntityIdTrait;

    /**
     * @var string|null
     */
    protected $userId;

    /**
     * @var string|null
     */
    protected $mimeType;

    /**
     * @var string|null
     */
    protected $fileExtension;

    /**
     * @var int|null
     */
    protected $fileSize;

    /**
     * @var string|null
     */
    protected $title;

    /**
     * @var string|null
     */
    protected $metaDataRaw;

    /**
     * @internal
     *
     * @var string|null
     */
    protected $mediaTypeRaw;

    /**
     * @var array<string, mixed>|null
     */
    protected $metaData;

    /**
     * @var MediaType|null
     */
    protected $mediaType;

    /**
     * @var \DateTimeInterface|null
     */
    protected $uploadedAt;

    /**
     * @var string|null
     */
    protected $alt;

    /**
     * @var string
     */
    protected $url = '';

    /**
     * @var string|null
     */
    protected $fileName;
    /**
     * @var MediaThumbnailCollection|null
     */
    protected $thumbnails;

    /**
     * @var UserEntity|null
     */
    protected $user;

    /**
     * @var MediaTranslationCollection|null
     */
    protected $translations;
    /**
     * @internal
     *
     * @var string|null
     */
    protected $thumbnailsRo;

    /**
     * @var bool
     */
    protected $private = false;

    /**
     * @internal
     */
    public function getThumbnailsRo(): ?string
    {
        $this->checkIfPropertyAccessIsAllowed('thumbnailsRo');

        return $this->thumbnailsRo;
    }

    public function isPrivate(): bool
    {
        return $this->private;
    }

    public function setPrivate(bool $private): void
    {
        $this->private = $private;
    }
    /**
     * @internal
     */
    public function setThumbnailsRo(string $thumbnailsRo): void
    {
        $this->thumbnailsRo = $thumbnailsRo;
    }

    public function getThumbnails(): ?MediaThumbnailCollection
    {
        return $this->thumbnails;
    }

    public function setThumbnails(MediaThumbnailCollection $thumbnailCollection): void
    {
        $this->thumbnails = $thumbnailCollection;
    }
    public function get(string $property)
    {
        if ($property === 'hasFile') {
            return $this->hasFile();
        }

        return parent::get($property);
    }

    public function getUserId(): ?string
    {
        return $this->userId;
    }

    public function setUserId(string $userId): void
    {
        $this->userId = $userId;
    }

    public function getMimeType(): ?string
    {
        return $this->mimeType;
    }

    public function setMimeType(string $mimeType): void
    {
        $this->mimeType = $mimeType;
    }

    public function getFileExtension(): ?string
    {
        return $this->fileExtension;
    }

    public function setFileExtension(string $fileExtension): void
    {
        $this->fileExtension = $fileExtension;
    }

    public function getFileSize(): ?int
    {
        return $this->fileSize;
    }

    public function setFileSize(int $fileSize): void
    {
        $this->fileSize = $fileSize;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    /**
     * @return array<string, mixed>|null
     */
    public function getMetaData(): ?array
    {
        return $this->metaData;
    }

    /**
     * @param array<string, mixed> $metaData
     */
    public function setMetaData(array $metaData): void
    {
        $this->metaData = $metaData;
    }

    public function getMediaType(): ?MediaType
    {
        return $this->mediaType;
    }

    public function setMediaType(MediaType $mediaType): void
    {
        $this->mediaType = $mediaType;
    }

    public function getUploadedAt(): ?\DateTimeInterface
    {
        return $this->uploadedAt;
    }

    public function setUploadedAt(\DateTimeInterface $uploadedAt): void
    {
        $this->uploadedAt = $uploadedAt;
    }

    public function getAlt(): ?string
    {
        return $this->alt;
    }

    public function setAlt(string $alt): void
    {
        $this->alt = $alt;
    }

    public function getUser(): ?UserEntity
    {
        return $this->user;
    }

    public function setUser(UserEntity $user): void
    {
        $this->user = $user;
    }

    public function getTranslations(): ?MediaTranslationCollection
    {
        return $this->translations;
    }

    public function setTranslations(MediaTranslationCollection $translations): void
    {
        $this->translations = $translations;
    }


    public function getAvatarUsers(): ?UserCollection
    {
        return $this->avatarUsers;
    }



    public function getUrl(): string
    {
        return $this->url;
    }

    public function setUrl(string $url): void
    {
        $this->url = $url;
    }

    public function hasFile(): bool
    {
        $hasFile = $this->mimeType !== null && $this->fileExtension !== null && $this->fileName !== null;

        return $this->hasFile = $hasFile;
    }

    public function getFileName(): ?string
    {
        return $this->fileName;
    }

    public function getFileNameIncludingExtension(): ?string
    {
        if ($this->fileName === null || $this->fileExtension === null) {
            return null;
        }

        return sprintf('%s.%s', $this->fileName, $this->fileExtension);
    }

    public function setFileName(string $fileName): void
    {
        $this->fileName = $fileName;
    }


    public function setMediaFolder(MediaFolderEntity $mediaFolder): void
    {
        $this->mediaFolder = $mediaFolder;
    }

    public function getMetaDataRaw(): ?string
    {
        return $this->metaDataRaw;
    }

    public function setMetaDataRaw(string $metaDataRaw): void
    {
        $this->metaDataRaw = $metaDataRaw;
    }

    /**
     * @internal
     */
    public function getMediaTypeRaw(): ?string
    {
        $this->checkIfPropertyAccessIsAllowed('mediaTypeRaw');

        return $this->mediaTypeRaw;
    }

    /**
     * @internal
     */
    public function setMediaTypeRaw(string $mediaTypeRaw): void
    {
        $this->mediaTypeRaw = $mediaTypeRaw;
    }
    /**
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        $data = parent::jsonSerialize();
        unset($data['metaDataRaw'], $data['mediaTypeRaw']);

        return $data;
    }

}
