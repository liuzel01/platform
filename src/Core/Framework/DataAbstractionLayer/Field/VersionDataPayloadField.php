<?php declare(strict_types=1);

namespace Shuwei\Core\Framework\DataAbstractionLayer\Field;

use Shuwei\Core\Framework\DataAbstractionLayer\FieldSerializer\VersionDataPayloadFieldSerializer;
use Shuwei\Core\Framework\Log\Package;

/**
 * @internal
 */
#[Package('core')]
class VersionDataPayloadField extends JsonField
{
    protected function getSerializerClass(): string
    {
        return VersionDataPayloadFieldSerializer::class;
    }
}
