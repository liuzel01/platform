<?php declare(strict_types=1);

namespace Shuwei\Core\Framework\Store\Exception;

use Shuwei\Core\Framework\Log\Package;
use Shuwei\Core\Framework\ShuweiHttpException;
use Symfony\Component\HttpFoundation\Response;

#[Package('merchant-services')]
class InvalidVariantIdException extends ShuweiHttpException
{
    public function __construct(
        array $parameters = [],
        ?\Throwable $e = null
    ) {
        parent::__construct('The variant id must be an non empty numeric value.', $parameters, $e);
    }

    public function getErrorCode(): string
    {
        return 'FRAMEWORK__INVALID_VARIANT_ID';
    }

    public function getStatusCode(): int
    {
        return Response::HTTP_BAD_REQUEST;
    }
}
