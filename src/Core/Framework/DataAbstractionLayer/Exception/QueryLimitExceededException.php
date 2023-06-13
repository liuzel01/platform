<?php declare(strict_types=1);

namespace Shuwei\Core\Framework\DataAbstractionLayer\Exception;

use Shuwei\Core\Framework\Log\Package;
use Shuwei\Core\Framework\ShuweiHttpException;
use Symfony\Component\HttpFoundation\Response;

#[Package('core')]
class QueryLimitExceededException extends ShuweiHttpException
{
    public function __construct(
        ?int $maxLimit,
        ?int $limit
    ) {
        parent::__construct(
            'The limit must be lower than or equal to MAX_LIMIT(={{ maxLimit }}). Given: {{ limit }}',
            ['maxLimit' => $maxLimit, 'limit' => $limit]
        );
    }

    public function getStatusCode(): int
    {
        return Response::HTTP_BAD_REQUEST;
    }

    public function getErrorCode(): string
    {
        return 'FRAMEWORK__QUERY_LIMIT_EXCEEDED';
    }
}
