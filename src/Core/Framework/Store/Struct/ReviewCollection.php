<?php declare(strict_types=1);

namespace Shuwei\Core\Framework\Store\Struct;

use Shuwei\Core\Framework\Log\Package;

/**
 * @codeCoverageIgnore
 */
#[Package('merchant-services')]
class ReviewCollection extends StoreCollection
{
    protected function getExpectedClass(): ?string
    {
        return ReviewStruct::class;
    }

    protected function getElementFromArray(array $element): StoreStruct
    {
        return ReviewStruct::fromArray($element);
    }
}
