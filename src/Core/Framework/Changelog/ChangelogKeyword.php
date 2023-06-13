<?php declare(strict_types=1);

namespace Shuwei\Core\Framework\Changelog;

use Shuwei\Core\Framework\Log\Package;

#[Package('core')]
enum ChangelogKeyword: string
{
    case ADDED = 'Added';
    case REMOVED = 'Removed';
    case CHANGED = 'Changed';
    case DEPRECATED = 'Deprecated';
}
