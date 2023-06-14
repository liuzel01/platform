<?php declare(strict_types=1);

namespace Shuwei\Administration\Events;

use Shuwei\Core\Framework\Context;
use Shuwei\Core\Framework\Event\ShuweiEvent;
use Shuwei\Core\Framework\Log\Package;
use Symfony\Contracts\EventDispatcher\Event;

#[Package('administration')]
class PreResetExcludedSearchTermEvent extends Event implements ShuweiEvent
{
    /**
     * @param array<string> $excludedTerms
     */
    public function __construct(
        private string $searchConfigId,
        private array $excludedTerms,
        private Context $context
    ) {
    }

    public function getSearchConfigId(): string
    {
        return $this->searchConfigId;
    }

    public function setSearchConfigId(string $searchConfigId): void
    {
        $this->searchConfigId = $searchConfigId;
    }

    /**
     * @return array<string>
     */
    public function getExcludedTerms(): array
    {
        return $this->excludedTerms;
    }

    /**
     * @param array<string> $excludedTerms
     */
    public function setExcludedTerms(array $excludedTerms): void
    {
        $this->excludedTerms = $excludedTerms;
    }

    public function getContext(): Context
    {
        return $this->context;
    }

    public function setContext(Context $context): void
    {
        $this->context = $context;
    }
}
