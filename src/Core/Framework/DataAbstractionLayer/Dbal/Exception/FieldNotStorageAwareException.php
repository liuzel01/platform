<?php declare(strict_types=1);

namespace Shuwei\Core\Framework\DataAbstractionLayer\Dbal\Exception;

use Shuwei\Core\Framework\Log\Package;
use Shuwei\Core\Framework\ShuweiHttpException;
use Symfony\Component\HttpFoundation\Response;

#[Package('core')]
class FieldNotStorageAwareException extends ShuweiHttpException
{
    public function __construct(string $field)
    {
        parent::__construct(
            'The field {{ field }} must implement the StorageAware interface to be accessible.',
            ['field' => $field]
        );
    }

    public function getStatusCode(): int
    {
        return Response::HTTP_BAD_REQUEST;
    }

    public function getErrorCode(): string
    {
        return 'FRAMEWORK__FIELD_IS_NOT_STORAGE_AWARE';
    }
}
