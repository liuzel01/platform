<?php declare(strict_types=1);

namespace Shuwei\Core\Framework\DataAbstractionLayer\Write\Validation;

use Shuwei\Core\Framework\Log\Package;

#[Package('core')]
class RestrictDeleteViolation
{
    /**
     * @param mixed[][] $restrictions
     */
    public function __construct(
        /**
         * Contains an array which indexed by definition class.
         * Each value represents a single restricted identity
         *
         * Example:
         *      [Shuwei\Core\Checkout\Cart\Price\Struct\QuantityPriceDefinition] => Array
         *          (
         *              [0] => c708bb9dc2c34243b9fb1fd6a676f2fb
         *              [1] => c708bb9dc2c34243b9fb1fd6a676f2fb
         *          )
         *      [Shuwei\Core\Content\Product\ProductDefinition] => Array
         *          (
         *              [0] => c708bb9dc2c34243b9fb1fd6a676f2fb
         *              [1] => c708bb9dc2c34243b9fb1fd6a676f2fb
         *          )
         */
        private readonly array $restrictions
    ) {
    }

    public function getRestrictions(): array
    {
        return $this->restrictions;
    }
}
