<?php
declare(strict_types=1);

namespace Shuwei\Core\Framework\DataAbstractionLayer\FieldSerializer;

use Shuwei\Core\Framework\Api\Context\AdminApiSource;
use Shuwei\Core\Framework\DataAbstractionLayer\DataAbstractionLayerException;
use Shuwei\Core\Framework\DataAbstractionLayer\Field\CreatedByField;
use Shuwei\Core\Framework\DataAbstractionLayer\Field\Field;
use Shuwei\Core\Framework\DataAbstractionLayer\Write\DataStack\KeyValuePair;
use Shuwei\Core\Framework\DataAbstractionLayer\Write\EntityExistence;
use Shuwei\Core\Framework\DataAbstractionLayer\Write\WriteParameterBag;
use Shuwei\Core\Framework\Log\Package;

/**
 * @internal
 */
#[Package('core')]
class CreatedByFieldSerializer extends FkFieldSerializer
{
    public function encode(Field $field, EntityExistence $existence, KeyValuePair $data, WriteParameterBag $parameters): \Generator
    {
        if (!($field instanceof CreatedByField)) {
            throw DataAbstractionLayerException::invalidSerializerField(CreatedByField::class, $field);
        }

        // only required for new entities
        if ($existence->exists()) {
            return;
        }

        $context = $parameters->getContext()->getContext();
        $scope = $context->getScope();

        if (!\in_array($scope, $field->getAllowedWriteScopes(), true)) {
            return;
        }

        if ($data->getValue()) {
            yield from parent::encode($field, $existence, $data, $parameters);

            return;
        }

        if (!$context->getSource() instanceof AdminApiSource) {
            return;
        }

        $userId = $context->getSource()->getUserId();

        if (!$userId) {
            return;
        }

        $data->setValue($userId);

        yield from parent::encode($field, $existence, $data, $parameters);
    }
}
