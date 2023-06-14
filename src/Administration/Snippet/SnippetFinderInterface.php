<?php declare(strict_types=1);

namespace Shuwei\Administration\Snippet;

use Shuwei\Core\Framework\Log\Package;

#[Package('administration')]
interface SnippetFinderInterface
{
    /**
     * @return array<string, mixed>
     */
    public function findSnippets(string $locale): array;
}
