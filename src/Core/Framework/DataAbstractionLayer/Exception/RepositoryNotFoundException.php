<?php declare(strict_types=1);

namespace Shuwei\Core\Framework\DataAbstractionLayer\Exception;

use Shuwei\Core\Framework\Log\Package;
use Shuwei\Core\Framework\ShuweiHttpException;

#[Package('core')]
class RepositoryNotFoundException extends ShuweiHttpException
{
    public function __construct(string $entity)
    {
        parent::__construct('Repository for entity "{{ entityName }}" does not exist.', ['entityName' => $entity]);
    }

    public function getErrorCode(): string
    {
        return 'FRAMEWORK__REPOSITORY_NOT_FOUND';
    }
}
