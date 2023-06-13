<?php declare(strict_types=1);

namespace Shuwei\Core\Framework\DataAbstractionLayer\Field;

use Shuwei\Core\Framework\DataAbstractionLayer\Dbal\FieldAccessorBuilder\CustomFieldsAccessorBuilder;
use Shuwei\Core\Framework\DataAbstractionLayer\FieldSerializer\CustomFieldsSerializer;
use Shuwei\Core\Framework\Log\Package;

#[Package('core')]
class CustomFields extends JsonField
{
    public function __construct(
        string $storageName = 'custom_fields',
        string $propertyName = 'customFields'
    ) {
        parent::__construct($storageName, $propertyName);
    }

    public function setPropertyMapping(array $propertyMapping): void
    {
        $this->propertyMapping = $propertyMapping;
    }

    protected function getSerializerClass(): string
    {
        return CustomFieldsSerializer::class;
    }

    protected function getAccessorBuilderClass(): ?string
    {
        return CustomFieldsAccessorBuilder::class;
    }
}
