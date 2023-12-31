<?php declare(strict_types=1);

namespace Shuwei\Core\Framework\DataAbstractionLayer\Search\Filter;

use Shuwei\Core\Framework\Log\Package;

/**
 * @final
 */
#[Package('core')]
class ContainsFilter extends SingleFieldFilter
{
    public function __construct(
        protected readonly string $field,
        protected mixed $value
    ) {
    }

    public function getField(): string
    {
        return $this->field;
    }

    public function getValue(): mixed
    {
        return $this->value;
    }

    public function getFields(): array
    {
        return [$this->field];
    }
}
