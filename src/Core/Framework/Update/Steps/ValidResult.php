<?php declare(strict_types=1);

namespace Shuwei\Core\Framework\Update\Steps;

use Shuwei\Core\Framework\Log\Package;

#[Package('system-settings')]
class ValidResult
{
    public function __construct(
        private readonly int $offset,
        private readonly int $total
    ) {
    }

    public function getOffset(): int
    {
        return $this->offset;
    }

    public function getTotal(): int
    {
        return $this->total;
    }
}
