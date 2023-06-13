<?php declare(strict_types=1);

namespace Shuwei\Core\Framework\DataAbstractionLayer\Field;

use Shuwei\Core\Framework\DataAbstractionLayer\Field\Flag\PrimaryKey;
use Shuwei\Core\Framework\DataAbstractionLayer\Field\Flag\Required;
use Shuwei\Core\Framework\DataAbstractionLayer\FieldSerializer\VersionFieldSerializer;
use Shuwei\Core\Framework\DataAbstractionLayer\Version\VersionDefinition;
use Shuwei\Core\Framework\Log\Package;

#[Package('core')]
class VersionField extends FkField
{
    public function __construct()
    {
        parent::__construct('version_id', 'versionId', VersionDefinition::class);

        $this->addFlags(new PrimaryKey(), new Required());
    }

    protected function getSerializerClass(): string
    {
        return VersionFieldSerializer::class;
    }
}
