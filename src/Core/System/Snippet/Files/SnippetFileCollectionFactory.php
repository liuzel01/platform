<?php declare(strict_types=1);

namespace Shuwei\Core\System\Snippet\Files;

use Shuwei\Core\Framework\Log\Package;

#[Package('system-settings')]
class SnippetFileCollectionFactory
{
    /**
     * @internal
     */
    public function __construct(private readonly SnippetFileLoaderInterface $snippetFileLoader)
    {
    }

    public function createSnippetFileCollection(): SnippetFileCollection
    {
        $collection = new SnippetFileCollection();
        $this->snippetFileLoader->loadSnippetFilesIntoCollection($collection);

        return $collection;
    }
}
