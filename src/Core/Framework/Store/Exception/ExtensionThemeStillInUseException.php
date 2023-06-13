<?php declare(strict_types=1);

namespace Shuwei\Core\Framework\Store\Exception;

use Shuwei\Core\Framework\Log\Package;
use Shuwei\Core\Framework\ShuweiHttpException;
use Symfony\Component\HttpFoundation\Response;

#[Package('merchant-services')]
class ExtensionThemeStillInUseException extends ShuweiHttpException
{
    public function __construct(
        string $id,
        array $parameters = [],
        ?\Throwable $e = null
    ) {
        $parameters['id'] = $id;

        parent::__construct(
            'The extension with id "{{id}}"can not be removed because it\'s theme is still assigned to a sales channel.',
            $parameters,
            $e
        );
    }

    public function getErrorCode(): string
    {
        return 'FRAMEWORK__EXTENSION_THEME_STILL_IN_USE';
    }

    public function getStatusCode(): int
    {
        return Response::HTTP_FORBIDDEN;
    }
}
