<?php declare(strict_types=1);

namespace Shuwei\Core\Framework\Api\Exception;

use Shuwei\Core\Framework\Log\Package;

/**
 * @deprecated tag:v6.6.0 - Will be removed, use Shuwei\Core\Framework\Api\Exception\ExpectationFailedException instead
 */
#[Package('core')]
class ExceptionFailedException extends ExpectationFailedException
{
}
