<?php declare(strict_types=1);

namespace Shuwei\Core\Framework\DataAbstractionLayer\Dbal\Exception;

use Shuwei\Core\Framework\Log\Package;
use Shuwei\Core\Framework\ShuweiHttpException;
use Symfony\Component\HttpFoundation\Response;

#[Package('core')]
class ParentAssociationCanNotBeFetched extends ShuweiHttpException
{
    public function __construct()
    {
        parent::__construct(
            'It is not possible to read the parent association directly. Please read the parents via a separate call over the repository'
        );
    }

    public function getStatusCode(): int
    {
        return Response::HTTP_BAD_REQUEST;
    }

    public function getErrorCode(): string
    {
        return 'FRAMEWORK__PARENT_ASSOCIATION_CAN_NOT_BE_FETCHED';
    }
}
