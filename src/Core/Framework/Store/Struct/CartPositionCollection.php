<?php declare(strict_types=1);

namespace Shuwei\Core\Framework\Store\Struct;

use Shuwei\Core\Framework\Log\Package;
use Shuwei\Core\Framework\Struct\Collection;

/**
 * @codeCoverageIgnore
 *
 * @extends Collection<CartPositionStruct>
 */
#[Package('merchant-services')]
class CartPositionCollection extends Collection
{
    public function __construct(iterable $elements = [])
    {
        foreach ($elements as $element) {
            if (\is_array($element)) {
                $element = $this->getElementFromArray($element);
            }

            $this->add($element);
        }
    }

    protected function getExpectedClass(): ?string
    {
        return CartPositionStruct::class;
    }

    protected function getElementFromArray(array $element): CartPositionStruct
    {
        return CartPositionStruct::fromArray($element);
    }
}
