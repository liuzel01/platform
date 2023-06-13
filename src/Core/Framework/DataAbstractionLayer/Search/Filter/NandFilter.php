<?php declare(strict_types=1);

namespace Shuwei\Core\Framework\DataAbstractionLayer\Search\Filter;

use Shuwei\Core\Framework\Log\Package;

/**
 * @final
 */
#[Package('core')]
class NandFilter extends NotFilter
{
    /**
     * @param Filter[] $queries
     */
    public function __construct(array $queries = [])
    {
        parent::__construct(self::CONNECTION_AND, $queries);
    }
}
