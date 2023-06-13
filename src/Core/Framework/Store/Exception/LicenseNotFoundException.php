<?php declare(strict_types=1);

namespace Shuwei\Core\Framework\Store\Exception;

use Shuwei\Core\Framework\Log\Package;
use Shuwei\Core\Framework\ShuweiHttpException;
use Symfony\Component\HttpFoundation\Response;

#[Package('merchant-services')]
class LicenseNotFoundException extends ShuweiHttpException
{
    public function __construct(
        int $licenseId,
        array $parameters = [],
        ?\Throwable $e = null
    ) {
        $parameters['licenseId'] = $licenseId;

        parent::__construct('Could not find license with id {{licenseId}}', $parameters, $e);
    }

    public function getErrorCode(): string
    {
        return 'FRAMEWORK__LICENSE_NOT_FOUND';
    }

    public function getStatusCode(): int
    {
        return Response::HTTP_NOT_FOUND;
    }
}
