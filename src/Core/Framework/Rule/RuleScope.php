<?php declare(strict_types=1);

namespace Shuwei\Core\Framework\Rule;

use Shuwei\Core\Framework\Context;
use Shuwei\Core\Framework\Log\Package;
use Shuwei\Core\System\Website\WebsiteContext;

#[Package('business-ops')]
abstract class RuleScope
{
    abstract public function getContext(): Context;

    abstract public function getWebsiteContext(): WebsiteContext;

    public function getCurrentTime(): \DateTimeImmutable
    {
        return new \DateTimeImmutable();
    }
}
