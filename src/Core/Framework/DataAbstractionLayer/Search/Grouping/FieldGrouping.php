<?php declare(strict_types=1);

namespace Shuwei\Core\Framework\DataAbstractionLayer\Search\Grouping;

use Shuwei\Core\Framework\DataAbstractionLayer\Search\CriteriaPartInterface;
use Shuwei\Core\Framework\Log\Package;
use Shuwei\Core\Framework\Struct\Struct;

/**
 * @final
 */
#[Package('core')]
class FieldGrouping extends Struct implements CriteriaPartInterface
{
    public function __construct(protected readonly string $field)
    {
    }

    public function getField(): string
    {
        return $this->field;
    }

    public function getFields(): array
    {
        return [$this->field];
    }
}
