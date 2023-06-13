<?php declare(strict_types=1);

namespace Shuwei\Core\Framework\Changelog;

use Shuwei\Core\Framework\Log\Package;
use Shuwei\Core\Framework\Struct\Collection;

/**
 * @internal
 *
 * @extends Collection<ChangelogFile>
 */
#[Package('core')]
class ChangelogFileCollection extends Collection
{
    protected function getExpectedClass(): ?string
    {
        return ChangelogFile::class;
    }
}
