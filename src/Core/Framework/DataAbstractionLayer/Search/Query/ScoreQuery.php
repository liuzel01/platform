<?php declare(strict_types=1);

namespace Shuwei\Core\Framework\DataAbstractionLayer\Search\Query;

use Shuwei\Core\Framework\DataAbstractionLayer\Search\Filter\Filter;
use Shuwei\Core\Framework\Log\Package;

/**
 * @final
 */
#[Package('core')]
class ScoreQuery extends Filter
{
    public function __construct(
        private readonly Filter $query,
        private readonly float $score,
        private readonly ?string $scoreField = null
    ) {
    }

    public function getFields(): array
    {
        return $this->query->getFields();
    }

    public function getScore(): float
    {
        return $this->score;
    }

    public function getQuery(): Filter
    {
        return $this->query;
    }

    public function getScoreField(): ?string
    {
        return $this->scoreField;
    }
}
