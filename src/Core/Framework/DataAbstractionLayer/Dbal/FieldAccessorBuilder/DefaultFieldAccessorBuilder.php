<?php declare(strict_types=1);

namespace Shuwei\Core\Framework\DataAbstractionLayer\Dbal\FieldAccessorBuilder;

use Shuwei\Core\Framework\Context;
use Shuwei\Core\Framework\DataAbstractionLayer\Dbal\EntityDefinitionQueryHelper;
use Shuwei\Core\Framework\DataAbstractionLayer\Dbal\Exception\FieldNotStorageAwareException;
use Shuwei\Core\Framework\DataAbstractionLayer\Field\Field;
use Shuwei\Core\Framework\DataAbstractionLayer\Field\StorageAware;
use Shuwei\Core\Framework\Log\Package;

/**
 * @internal
 */
#[Package('core')]
class DefaultFieldAccessorBuilder implements FieldAccessorBuilderInterface
{
    public function buildAccessor(string $root, Field $field, Context $context, string $accessor): string
    {
        if (!$field instanceof StorageAware) {
            throw new FieldNotStorageAwareException($root . '.' . $field->getPropertyName());
        }

        return EntityDefinitionQueryHelper::escape($root) . '.' . EntityDefinitionQueryHelper::escape($field->getStorageName());
    }
}
