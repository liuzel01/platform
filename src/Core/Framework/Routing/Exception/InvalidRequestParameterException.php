<?php declare(strict_types=1);

namespace Shuwei\Core\Framework\Routing\Exception;

use Shuwei\Core\Framework\Feature;
use Shuwei\Core\Framework\Log\Package;
use Shuwei\Core\Framework\Routing\RoutingException;
use Symfony\Component\HttpFoundation\Response;

/**
 * @deprecated tag:v6.6.0 - will be removed, use RoutingException::invalidRequestParameter instead
 */
#[Package('core')]
class InvalidRequestParameterException extends RoutingException
{
    public function __construct(string $name)
    {
        Feature::triggerDeprecationOrThrow(
            'v6.6.0.0',
            Feature::deprecatedClassMessage(self::class, 'v6.6.0.0', 'use RoutingException::invalidRequestParameter instead')
        );

        parent::__construct(
            Response::HTTP_BAD_REQUEST,
            self::INVALID_REQUEST_PARAMETER_CODE,
            'The parameter "{{ parameter }}" is invalid.',
            ['parameter' => $name]
        );
    }
}
