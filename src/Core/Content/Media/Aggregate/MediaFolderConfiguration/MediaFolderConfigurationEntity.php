<?php declare(strict_types=1);

namespace Shuwei\Core\Content\Media\Aggregate\MediaFolderConfiguration;

use Shuwei\Core\Content\Media\Aggregate\MediaFolder\MediaFolderCollection;
use Shuwei\Core\Content\Media\Aggregate\MediaThumbnailSize\MediaThumbnailSizeCollection;
use Shuwei\Core\Framework\DataAbstractionLayer\Entity;
use Shuwei\Core\Framework\DataAbstractionLayer\EntityCustomFieldsTrait;
use Shuwei\Core\Framework\DataAbstractionLayer\EntityIdTrait;
use Shuwei\Core\Framework\Log\Package;

#[Package('content')]
class MediaFolderConfigurationEntity extends Entity
{
    use EntityCustomFieldsTrait;
    use EntityIdTrait;

    /**
     * @var MediaFolderCollection
     */
    protected $mediaFolders;

    /**
     * @var bool
     */
    protected $createThumbnails;

    /**
     * @var bool
     */
    protected $keepAspectRatio;

    /**
     * @var int
     */
    protected $thumbnailQuality;

    /**
     * @var bool
     */
    protected $private;

    /**
     * @var bool|null
     */
    protected $noAssociation;

    /**
     * @var MediaThumbnailSizeCollection|null
     */
    protected $mediaThumbnailSizes;

    /**
     * @internal
     *
     * @var string|null
     */
    protected $mediaThumbnailSizesRo;

    public function getMediaFolders(): ?MediaFolderCollection
    {
        return $this->mediaFolders;
    }

    public function setMediaFolders(MediaFolderCollection $mediaFolders): void
    {
        $this->mediaFolders = $mediaFolders;
    }

    public function getCreateThumbnails(): bool
    {
        return $this->createThumbnails;
    }

    public function setCreateThumbnails(bool $createThumbnails): void
    {
        $this->createThumbnails = $createThumbnails;
    }

    public function getKeepAspectRatio(): bool
    {
        return $this->keepAspectRatio;
    }

    public function setKeepAspectRatio(bool $keepAspectRatio): void
    {
        $this->keepAspectRatio = $keepAspectRatio;
    }

    public function getMediaThumbnailSizes(): ?MediaThumbnailSizeCollection
    {
        return $this->mediaThumbnailSizes;
    }

    public function setMediaThumbnailSizes(MediaThumbnailSizeCollection $mediaThumbnailSizes): void
    {
        $this->mediaThumbnailSizes = $mediaThumbnailSizes;
    }

    public function getThumbnailQuality(): int
    {
        return $this->thumbnailQuality;
    }

    public function setThumbnailQuality(int $thumbnailQuality): void
    {
        $this->thumbnailQuality = $thumbnailQuality;
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
    public function getMediaThumbnailSizesRo(): ?string
    {
        $this->checkIfPropertyAccessIsAllowed('mediaThumbnailSizesRo');

        return $this->mediaThumbnailSizesRo;
    }

    /**
     * @internal
     */
    public function setMediaThumbnailSizesRo(string $mediaThumbnailSizesRo): void
    {
        $this->mediaThumbnailSizesRo = $mediaThumbnailSizesRo;
    }

    public function isNoAssociation(): ?bool
    {
        return $this->noAssociation;
    }

    public function setNoAssociation(?bool $noAssociation): void
    {
        $this->noAssociation = $noAssociation;
    }
}
