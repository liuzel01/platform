<?php declare(strict_types=1);

namespace Shuwei\Core\Framework\Store\Struct;

use Shuwei\Core\Framework\Log\Package;

/**
 * @codeCoverageIgnore
 */
#[Package('merchant-services')]
class BinaryCollection extends StoreCollection
{
    protected function getExpectedClass(): ?string
    {
        return BinaryStruct::class;
    }

    protected function getElementFromArray(array $element): StoreStruct
    {
        return BinaryStruct::fromArray($element);
    }
}
