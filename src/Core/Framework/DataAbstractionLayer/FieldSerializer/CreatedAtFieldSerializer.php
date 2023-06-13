<?php
declare(strict_types=1);

namespace Shuwei\Core\Framework\DataAbstractionLayer\FieldSerializer;

use Shuwei\Core\Framework\DataAbstractionLayer\Field\Field;
use Shuwei\Core\Framework\DataAbstractionLayer\Write\DataStack\KeyValuePair;
use Shuwei\Core\Framework\DataAbstractionLayer\Write\EntityExistence;
use Shuwei\Core\Framework\DataAbstractionLayer\Write\WriteParameterBag;
use Shuwei\Core\Framework\Log\Package;

/**
 * @internal
 */
#[Package('core')]
class CreatedAtFieldSerializer extends DateTimeFieldSerializer
{
    public function encode(Field $field, EntityExistence $existence, KeyValuePair $data, WriteParameterBag $parameters): \Generator
    {
        if ($existence->exists()) {
            return;
        }

        if (!$data->getValue()) {
            $data->setValue(new \DateTime());
        }

        yield from parent::encode($field, $existence, $data, $parameters);
    }
}
