<?php declare(strict_types=1);

namespace Shuwei\Core\Framework\Changelog;

use Shuwei\Core\Framework\Log\Package;

#[Package('core')]
enum ChangelogSection: string
{
    case core = 'Core';
    case api = 'API';
    case administration = 'Administration';
    case frontend = 'Frontend';
    case upgrade = 'Upgrade Information';
    case major = 'Next Major Version Changes';
}
