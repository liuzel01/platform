<?php declare(strict_types=1);

namespace Shuwei\Core\Framework\Store\Services;

use Psr\Http\Message\ResponseInterface;
use Shuwei\Core\Framework\Log\Package;

/**
 * @internal
 */
#[Package('merchant-services')]
interface MiddlewareInterface
{
    public function __invoke(ResponseInterface $response): ResponseInterface;
}
