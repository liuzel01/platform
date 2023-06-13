<?php declare(strict_types=1);

namespace Shuwei\Core\Framework\Routing\Exception;

use Shuwei\Core\Framework\Log\Package;
use Shuwei\Core\Framework\ShuweiHttpException;
use Symfony\Component\HttpFoundation\Response;

#[Package('core')]
class InvalidRouteScopeException extends ShuweiHttpException
{
    public function __construct(string $routeName)
    {
        parent::__construct(
            'Invalid route scope for route {{ routeName }}.',
            ['routeName' => $routeName]
        );
    }

    public function getErrorCode(): string
    {
        return 'FRAMEWORK__ROUTING_INVALID_ROUTE_SCOPE';
    }

    public function getStatusCode(): int
    {
        return Response::HTTP_PRECONDITION_FAILED;
    }
}
