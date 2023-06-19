<?php declare(strict_types=1);

namespace Shuwei\Core\Content\Media\Aggregate\MediaFolderConfiguration;

use Shuwei\Core\Framework\DataAbstractionLayer\EntityCollection;
use Shuwei\Core\Framework\Log\Package;

/**
 * @extends EntityCollection<MediaFolderConfigurationEntity>
 */
#[Package('content')]
class MediaFolderConfigurationCollection extends EntityCollection
{
    public function getApiAlias(): string
    {
        return 'media_folder_configuration_collection';
    }

    protected function getExpectedClass(): string
    {
        return MediaFolderConfigurationEntity::class;
    }
}
