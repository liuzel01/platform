<?php declare(strict_types=1);

namespace Shuwei\Core\System\Snippet\Filter;

use Shuwei\Core\Framework\Log\Package;
use Shuwei\Core\System\Snippet\Exception\FilterNotFoundException;

#[Package('system-settings')]
class SnippetFilterFactory
{
    /**
     * @internal
     */
    public function __construct(private readonly iterable $filters)
    {
    }

    /**
     * @throws \Exception
     */
    public function getFilter(string $name): SnippetFilterInterface
    {
        /** @var SnippetFilterInterface $filter */
        foreach ($this->filters as $filter) {
            if ($filter->supports($name)) {
                return $filter;
            }
        }

        throw new FilterNotFoundException($name, self::class);
    }
}
