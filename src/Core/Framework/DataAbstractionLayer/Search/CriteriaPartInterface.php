<?php declare(strict_types=1);

namespace Shuwei\Core\Framework\DataAbstractionLayer\Search;

use Shuwei\Core\Framework\Log\Package;

/**
 * @internal
 */
#[Package('core')]
interface CriteriaPartInterface
{
    /**
     * @return list<string>
     */
    public function getFields(): array;
}
