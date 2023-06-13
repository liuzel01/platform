<?php declare(strict_types=1);

namespace Shuwei\Core\Framework\DataAbstractionLayer\Dbal\FieldAccessorBuilder;

use Shuwei\Core\Framework\Context;
use Shuwei\Core\Framework\DataAbstractionLayer\Field\ConfigJsonField;
use Shuwei\Core\Framework\DataAbstractionLayer\Field\Field;
use Shuwei\Core\Framework\Log\Package;

/**
 * @internal
 */
#[Package('core')]
class ConfigJsonFieldAccessorBuilder extends JsonFieldAccessorBuilder
{
    public function buildAccessor(string $root, Field $field, Context $context, string $accessor): ?string
    {
        if (!$field instanceof ConfigJsonField) {
            return null;
        }

        $jsonPath = preg_replace(
            '#^' . preg_quote($field->getPropertyName(), '#') . '#',
            '',
            $accessor
        );

        $accessor = $field->getPropertyName() . '.' . ConfigJsonField::STORAGE_KEY . $jsonPath;

        return parent::buildAccessor($root, $field, $context, $accessor);
    }
}
