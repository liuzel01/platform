<?php declare(strict_types=1);

namespace Shuwei\Core\System\Snippet\Files;

use Shuwei\Core\Framework\Log\Package;

#[Package('system-settings')]
interface SnippetFileLoaderInterface
{
    public function loadSnippetFilesIntoCollection(SnippetFileCollection $snippetFileCollection): void;
}
