<?php declare(strict_types=1);

namespace Shuwei\Core\System\Snippet\Struct;

use Shuwei\Core\Framework\Log\Package;
use Shuwei\Core\Framework\Struct\Collection;

/**
 * @extends Collection<MissingSnippetStruct>
 */
#[Package('system-settings')]
class MissingSnippetCollection extends Collection
{
}
