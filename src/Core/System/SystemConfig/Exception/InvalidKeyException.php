<?php declare(strict_types=1);

namespace Shuwei\Core\System\SystemConfig\Exception;

use Shuwei\Core\Framework\Log\Package;
use Shuwei\Core\Framework\ShuweiHttpException;
use Symfony\Component\HttpFoundation\Response;

#[Package('system-settings')]
class InvalidKeyException extends ShuweiHttpException
{
    public function __construct(string $key)
    {
        parent::__construct('Invalid key \'{{ key }}\'', ['key' => $key]);
    }

    public function getStatusCode(): int
    {
        return Response::HTTP_BAD_REQUEST;
    }

    public function getErrorCode(): string
    {
        return 'SYSTEM__INVALID_KEY';
    }
}
