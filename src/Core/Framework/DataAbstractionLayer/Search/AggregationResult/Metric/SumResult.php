<?php declare(strict_types=1);

namespace Shuwei\Core\Framework\DataAbstractionLayer\Search\AggregationResult\Metric;

use Shuwei\Core\Framework\DataAbstractionLayer\Search\AggregationResult\AggregationResult;
use Shuwei\Core\Framework\Log\Package;

/**
 * @final
 */
#[Package('core')]
class SumResult extends AggregationResult
{
    public function __construct(
        string $name,
        protected float $sum
    ) {
        parent::__construct($name);
    }

    public function getSum(): float
    {
        return $this->sum;
    }
}
