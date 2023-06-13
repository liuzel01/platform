<?php declare(strict_types=1);

namespace Shuwei\Core\Framework\DataAbstractionLayer\Dbal\FieldResolver;

use Shuwei\Core\Framework\Log\Package;

/**
 * @internal
 */
#[Package('core')]
abstract class AbstractFieldResolver
{
    abstract public function join(FieldResolverContext $context): string;
}
