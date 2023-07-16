<?php declare(strict_types=1);

namespace Shuwei\Core\Framework\Rule;

use Shuwei\Core\Framework\Context;
use Shuwei\Core\Framework\Log\Package;
use Frontend\FrontendContext;

#[Package('business-ops')]
abstract class RuleScope
{
    abstract public function getContext(): Context;

    abstract public function getFrontendContext(): FrontendContext;

    public function getCurrentTime(): \DateTimeImmutable
    {
        return new \DateTimeImmutable();
    }
}
