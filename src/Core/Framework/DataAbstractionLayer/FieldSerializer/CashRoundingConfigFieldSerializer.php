<?php declare(strict_types=1);

namespace Shuwei\Core\Framework\DataAbstractionLayer\FieldSerializer;

use Shuwei\Core\Framework\DataAbstractionLayer\Field\Field;
use Shuwei\Core\Framework\DataAbstractionLayer\Pricing\CashRoundingConfig;
use Shuwei\Core\Framework\DataAbstractionLayer\Write\DataStack\KeyValuePair;
use Shuwei\Core\Framework\DataAbstractionLayer\Write\EntityExistence;
use Shuwei\Core\Framework\DataAbstractionLayer\Write\WriteParameterBag;
use Shuwei\Core\Framework\Log\Package;

/**
 * @internal
 */
#[Package('core')]
class CashRoundingConfigFieldSerializer extends JsonFieldSerializer
{
    public function encode(
        Field $field,
        EntityExistence $existence,
        KeyValuePair $data,
        WriteParameterBag $parameters
    ): \Generator {
        if ($data->getValue() !== null) {
            $value = $data->getValue();
            unset($value['extensions']);

            $data = new KeyValuePair($data->getKey(), $value, $data->isRaw());
        }

        yield from parent::encode($field, $existence, $data, $parameters);
    }

    public function decode(Field $field, mixed $value): ?CashRoundingConfig
    {
        if ($value === null) {
            return null;
        }

        $raw = json_decode((string) $value, true, 512, \JSON_THROW_ON_ERROR);

        return new CashRoundingConfig(
            (int) $raw['decimals'],
            (float) $raw['interval'],
            (bool) $raw['roundForNet']
        );
    }
}
