<?php declare(strict_types=1);

namespace Shuwei\Core\Framework\DataAbstractionLayer\Exception;

use Shuwei\Core\Framework\Log\Package;
use Shuwei\Core\Framework\ShuweiHttpException;
use Symfony\Component\HttpFoundation\Response;

#[Package('core')]
class ReadProtectedException extends ShuweiHttpException
{
    public function __construct(
        string $field,
        string $scope
    ) {
        parent::__construct(
            'The field/association "{{ field }}" is read protected for your scope "{{ scope }}"',
            [
                'field' => $field,
                'scope' => $scope,
            ]
        );
    }

    public function getErrorCode(): string
    {
        return 'FRAMEWORK__READ_PROTECTED';
    }

    public function getStatusCode(): int
    {
        return Response::HTTP_FORBIDDEN;
    }
}
