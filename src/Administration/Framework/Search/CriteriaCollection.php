<?php declare(strict_types=1);

namespace Shuwei\Administration\Framework\Search;

use Shuwei\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shuwei\Core\Framework\Log\Package;
use Shuwei\Core\Framework\Struct\Collection;

/**
 * @extends Collection<Criteria>
 */
#[Package('administration')]
class CriteriaCollection extends Collection
{
    protected function getExpectedClass(): ?string
    {
        return Criteria::class;
    }
}
