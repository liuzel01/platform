<?php declare(strict_types=1);

namespace Shuwei\Core\Framework\Routing;

use Shuwei\Core\Framework\Feature;
use Shuwei\Core\Framework\HttpException;
use Shuwei\Core\Framework\Log\Package;
use Shuwei\Core\Framework\Routing\Exception\InvalidRequestParameterException;
use Shuwei\Core\Framework\Routing\Exception\MissingRequestParameterException;
use Symfony\Component\HttpFoundation\Response;

#[Package('core')]
class RoutingException extends HttpException
{
    public const MISSING_REQUEST_PARAMETER_CODE = 'FRAMEWORK__MISSING_REQUEST_PARAMETER';

    public const INVALID_REQUEST_PARAMETER_CODE = 'FRAMEWORK__INVALID_REQUEST_PARAMETER';

    public static function invalidRequestParameter(string $name): self
    {
        if (!Feature::isActive('v6.6.0.0')) {
            return new InvalidRequestParameterException($name);
        }

        return new self(
            Response::HTTP_BAD_REQUEST,
            self::INVALID_REQUEST_PARAMETER_CODE,
            'The parameter "{{ parameter }}" is invalid.',
            ['parameter' => $name]
        );
    }

    public static function missingRequestParameter(string $name, string $path = ''): self
    {
        if (!Feature::isActive('v6.6.0.0')) {
            return new MissingRequestParameterException($name, $path);
        }

        return new self(
            Response::HTTP_BAD_REQUEST,
            self::MISSING_REQUEST_PARAMETER_CODE,
            'Parameter "{{ parameterName }}" is missing.',
            ['parameterName' => $name, 'path' => $path]
        );
    }
}
