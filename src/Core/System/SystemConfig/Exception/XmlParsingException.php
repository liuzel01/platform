<?php declare(strict_types=1);

namespace Shuwei\Core\System\SystemConfig\Exception;

use Shuwei\Core\Framework\Log\Package;
use Shuwei\Core\Framework\ShuweiHttpException;
use Symfony\Component\HttpFoundation\Response;

#[Package('system-settings')]
class XmlParsingException extends ShuweiHttpException
{
    public function __construct(
        string $xmlFile,
        string $message
    ) {
        parent::__construct(
            'Unable to parse file "{{ file }}". Message: {{ message }}',
            ['file' => $xmlFile, 'message' => $message]
        );
    }

    public function getErrorCode(): string
    {
        return 'SYSTEM__XML_PARSE_ERROR';
    }

    public function getStatusCode(): int
    {
        return Response::HTTP_BAD_REQUEST;
    }
}
