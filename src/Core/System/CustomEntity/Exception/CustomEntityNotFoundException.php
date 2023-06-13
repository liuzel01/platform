<?php declare(strict_types=1);

namespace Shuwei\Core\System\CustomEntity\Exception;

use Shuwei\Core\Framework\Log\Package;
use Shuwei\Core\Framework\ShuweiHttpException;
use Symfony\Component\HttpFoundation\Response;

#[Package('core')]
class CustomEntityNotFoundException extends ShuweiHttpException
{
    public function __construct(string $customEntity)
    {
        parent::__construct(
            'Custom Entity "{{ entityName }}" does not exist.',
            ['entityName' => $customEntity]
        );
    }

    public function getErrorCode(): string
    {
        return 'FRAMEWORK__CUSTOM_ENTITY_NOT_FOUND';
    }

    public function getStatusCode(): int
    {
        return Response::HTTP_NOT_FOUND;
    }
}
