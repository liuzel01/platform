<?php declare(strict_types=1);

namespace Shuwei\Core\System\Snippet;

use Shuwei\Core\Framework\Log\Package;

#[Package('system-settings')]
interface SnippetValidatorInterface
{
    public function validate(): array;
}
