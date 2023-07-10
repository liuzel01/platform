<?php declare(strict_types=1);

namespace Shuwei\Core\DevOps\Test\Environment\_fixtures;

use Shuwei\Core\DevOps\Environment\EnvironmentHelperTransformerData;
use Shuwei\Core\DevOps\Environment\EnvironmentHelperTransformerInterface;
use Shuwei\Core\Framework\Log\Package;

/**
 * @internal
 */
#[Package('core')]
class EnvironmentHelperTransformer implements EnvironmentHelperTransformerInterface
{
    public static function transform(EnvironmentHelperTransformerData $data): void
    {
        $data->setValue($data->getValue() !== null ? $data->getValue() . ' bar' : null);
        $data->setDefault($data->getDefault() . ' baz');
    }
}
