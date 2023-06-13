<?php declare(strict_types=1);

namespace Shuwei\Core\Framework\DataAbstractionLayer\Exception;

use Shuwei\Core\Framework\Log\Package;
use Shuwei\Core\Framework\ShuweiHttpException;

#[Package('core')]
class AssociationNotFoundException extends ShuweiHttpException
{
    public function __construct(string $field)
    {
        parent::__construct(
            'Can not find association by name {{ association }}',
            ['association' => $field]
        );
    }

    public function getErrorCode(): string
    {
        return 'FRAMEWORK__ASSOCIATION_NOT_FOUND';
    }
}
