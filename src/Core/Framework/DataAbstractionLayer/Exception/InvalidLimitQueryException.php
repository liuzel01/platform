<?php declare(strict_types=1);

namespace Shuwei\Core\Framework\DataAbstractionLayer\Exception;

use Shuwei\Core\Framework\Log\Package;
use Shuwei\Core\Framework\ShuweiHttpException;
use Symfony\Component\HttpFoundation\Response;

#[Package('core')]
class InvalidLimitQueryException extends ShuweiHttpException
{
    public function __construct(mixed $limit)
    {
        parent::__construct(
            'The limit parameter must be a positive integer greater or equals than 1. Given: {{ limit }}',
            ['limit' => $limit]
        );
    }

    public function getStatusCode(): int
    {
        return Response::HTTP_BAD_REQUEST;
    }

    public function getErrorCode(): string
    {
        return 'FRAMEWORK__INVALID_LIMIT_QUERY';
    }
}
