<?php declare(strict_types=1);

namespace Shuwei\Core\Framework\DataAbstractionLayer\Search\AggregationResult;

use Shuwei\Core\Framework\Log\Package;
use Shuwei\Core\Framework\Struct\Struct;

/**
 * @internal
 */
#[Package('core')]
abstract class AggregationResult extends Struct
{
    public function __construct(protected string $name)
    {
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getApiAlias(): string
    {
        return $this->name . '_aggregation';
    }
}
