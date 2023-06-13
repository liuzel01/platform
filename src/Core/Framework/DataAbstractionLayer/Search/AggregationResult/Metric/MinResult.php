<?php declare(strict_types=1);

namespace Shuwei\Core\Framework\DataAbstractionLayer\Search\AggregationResult\Metric;

use Shuwei\Core\Framework\DataAbstractionLayer\Search\AggregationResult\AggregationResult;
use Shuwei\Core\Framework\Log\Package;

/**
 * @final
 */
#[Package('core')]
class MinResult extends AggregationResult
{
    public function __construct(
        string $name,
        protected float|int|string|null $min
    ) {
        parent::__construct($name);
    }

    public function getMin(): float|int|string|null
    {
        return $this->min;
    }
}
