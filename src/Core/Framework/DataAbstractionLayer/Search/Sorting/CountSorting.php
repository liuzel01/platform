<?php declare(strict_types=1);

namespace Shuwei\Core\Framework\DataAbstractionLayer\Search\Sorting;

use Shuwei\Core\Framework\Log\Package;

/**
 * @final
 */
#[Package('core')]
class CountSorting extends FieldSorting
{
    protected string $type = 'count';
}
