<?php declare(strict_types=1);

namespace Shuwei\Core\Framework\Store\Services;

use Shuwei\Core\Framework\Context;
use Shuwei\Core\Framework\Log\Package;
use Shuwei\Core\Framework\Store\Struct\ReviewStruct;

/**
 * @internal
 */
#[Package('merchant-services')]
abstract class AbstractExtensionStoreLicensesService
{
    abstract public function cancelSubscription(int $licenseId, Context $context): void;

    abstract public function rateLicensedExtension(ReviewStruct $rating, Context $context): void;

    abstract protected function getDecorated(): AbstractExtensionStoreLicensesService;
}
