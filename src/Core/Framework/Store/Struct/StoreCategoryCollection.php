<?php declare(strict_types=1);

namespace Shuwei\Core\Framework\Store\Struct;

use Shuwei\Core\Framework\Log\Package;

/**
 * @codeCoverageIgnore
 */
#[Package('merchant-services')]
class StoreCategoryCollection extends StoreCollection
{
    protected function getExpectedClass(): ?string
    {
        return StoreCategoryStruct::class;
    }

    protected function getElementFromArray(array $element): StoreStruct
    {
        return StoreCategoryStruct::fromArray($element);
    }
}
