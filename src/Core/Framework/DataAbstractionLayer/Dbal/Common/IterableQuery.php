<?php declare(strict_types=1);

namespace Shuwei\Core\Framework\DataAbstractionLayer\Dbal\Common;

use Doctrine\DBAL\Query\QueryBuilder;
use Shuwei\Core\Framework\Log\Package;

/**
 * @internal
 */
#[Package('core')]
interface IterableQuery
{
    /**
     * @return array<string|int, mixed>
     */
    public function fetch(): array;

    public function fetchCount(): int;

    public function getQuery(): QueryBuilder;

    /**
     * @return array{offset: int|null}
     */
    public function getOffset(): array;
}
