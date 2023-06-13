<?php declare(strict_types=1);

namespace Shuwei\Core\Framework\DataAbstractionLayer\Exception;

use Shuwei\Core\Framework\Log\Package;
use Shuwei\Core\Framework\ShuweiHttpException;
use Symfony\Component\HttpFoundation\Response;

#[Package('core')]
class InvalidFilterQueryException extends ShuweiHttpException
{
    public function __construct(
        string $message,
        private readonly string $path = ''
    ) {
        parent::__construct('{{ message }}', ['message' => $message]);
    }

    public function getStatusCode(): int
    {
        return Response::HTTP_BAD_REQUEST;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function getErrorCode(): string
    {
        return 'FRAMEWORK__INVALID_FILTER_QUERY';
    }
}
