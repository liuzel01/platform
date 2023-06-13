<?php declare(strict_types=1);

namespace Shuwei\Core\System\Snippet;

use Shuwei\Core\Framework\DataAbstractionLayer\EntityCollection;
use Shuwei\Core\Framework\Log\Package;

/**
 * @extends EntityCollection<SnippetEntity>
 */
#[Package('core')]
class SnippetCollection extends EntityCollection
{
    public function getApiAlias(): string
    {
        return 'snippet_collection';
    }

    protected function getExpectedClass(): string
    {
        return SnippetEntity::class;
    }
}
