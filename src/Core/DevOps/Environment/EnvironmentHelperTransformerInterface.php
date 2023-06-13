<?php declare(strict_types=1);

namespace Shuwei\Core\DevOps\Environment;

use Shuwei\Core\Framework\Log\Package;

#[Package('core')]
interface EnvironmentHelperTransformerInterface
{
    public static function transform(EnvironmentHelperTransformerData $data): void;
}
