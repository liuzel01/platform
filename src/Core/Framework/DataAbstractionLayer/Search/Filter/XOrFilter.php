<?php declare(strict_types=1);

namespace Shuwei\Core\Framework\DataAbstractionLayer\Search\Filter;

use Shuwei\Core\Framework\Log\Package;

/**
 * @final
 */
#[Package('core')]
class XOrFilter extends MultiFilter
{
    /**
     * @param  Filter[] $queries
     */
    public function __construct(array $queries = [])
    {
        parent::__construct(self::CONNECTION_XOR, $queries);
    }
}
