<?php declare(strict_types=1);

namespace Shuwei\Core\Content\Media\File;

use Shuwei\Core\Framework\Log\Package;

#[Package('content')]
interface FileUrlValidatorInterface
{
    public function isValid(string $source): bool;
}
