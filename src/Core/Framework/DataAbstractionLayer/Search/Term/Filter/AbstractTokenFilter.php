<?php declare(strict_types=1);

namespace Shuwei\Core\Framework\DataAbstractionLayer\Search\Term\Filter;

use Shuwei\Core\Framework\Context;
use Shuwei\Core\Framework\Log\Package;
use Symfony\Contracts\Service\ResetInterface;

#[Package('core')]
abstract class AbstractTokenFilter implements ResetInterface
{
    public function reset(): void
    {
        $this->getDecorated()->reset();
    }

    abstract public function getDecorated(): AbstractTokenFilter;

    abstract public function filter(array $tokens, Context $context): array;
}
