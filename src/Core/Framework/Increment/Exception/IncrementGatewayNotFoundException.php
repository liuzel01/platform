<?php declare(strict_types=1);

namespace Shuwei\Core\Framework\Increment\Exception;

use Shuwei\Core\Framework\Log\Package;
use Shuwei\Core\Framework\ShuweiHttpException;
use Symfony\Component\HttpFoundation\Response;

#[Package('core')]
class IncrementGatewayNotFoundException extends ShuweiHttpException
{
    public function __construct(string $pool)
    {
        parent::__construct(
            'Increment gateway for pool "{{ pool }}" was not found.',
            ['pool' => $pool]
        );
    }

    public function getStatusCode(): int
    {
        return Response::HTTP_BAD_REQUEST;
    }

    public function getErrorCode(): string
    {
        return 'FRAMEWORK__INCREMENT_GATEWAY_NOT_FOUND';
    }
}
