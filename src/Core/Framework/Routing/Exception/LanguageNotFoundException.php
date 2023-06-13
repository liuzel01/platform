<?php declare(strict_types=1);

namespace Shuwei\Core\Framework\Routing\Exception;

use Shuwei\Core\Framework\Log\Package;
use Shuwei\Core\Framework\ShuweiHttpException;
use Symfony\Component\HttpFoundation\Response;

#[Package('core')]
class LanguageNotFoundException extends ShuweiHttpException
{
    final public const LANGUAGE_NOT_FOUND_ERROR = 'FRAMEWORK__LANGUAGE_NOT_FOUND';

    public function __construct(?string $languageId)
    {
        parent::__construct(
            'The language "{{ languageId }}" was not found.',
            ['languageId' => $languageId]
        );
    }

    public function getStatusCode(): int
    {
        return Response::HTTP_PRECONDITION_FAILED;
    }

    public function getErrorCode(): string
    {
        return self::LANGUAGE_NOT_FOUND_ERROR;
    }
}
