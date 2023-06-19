<?php declare(strict_types=1);

namespace Shuwei\Core\Installer\Requirements;

use Shuwei\Core\Framework\Log\Package;

/**
 * @internal
 */
#[Package('core
Extracted to be able to mock all ini values')]
class IniConfigReader
{
    public function get(string $key): string
    {
        return (string) \ini_get($key);
    }
}
