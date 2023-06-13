<?php declare(strict_types=1);

namespace Shuwei\Core\Framework;

use Shuwei\Core\Framework\Log\Package;

#[Package('core')]
interface ShuweiException extends \Throwable
{
    public function getErrorCode(): string;

    /**
     * @return array<string|int, mixed|null>
     */
    public function getParameters(): array;
}
