<?php declare(strict_types=1);

namespace Shuwei\Core\Framework\DataAbstractionLayer\Write\FieldException;

use Shuwei\Core\Framework\Log\Package;
use Shuwei\Core\Framework\ShuweiHttpException;
use Symfony\Component\HttpFoundation\Response;

#[Package('core')]
class ExpectedArrayException extends ShuweiHttpException implements WriteFieldException
{
    public function __construct(private readonly string $path)
    {
        parent::__construct('Expected data to be array.');
    }

    public function getErrorCode(): string
    {
        return 'FRAMEWORK__WRITE_MALFORMED_INPUT';
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function getStatusCode(): int
    {
        return Response::HTTP_BAD_REQUEST;
    }
}
