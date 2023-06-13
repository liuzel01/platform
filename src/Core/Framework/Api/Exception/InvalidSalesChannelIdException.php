<?php declare(strict_types=1);

namespace Shuwei\Core\Framework\Api\Exception;

use Shuwei\Core\Framework\Log\Package;
use Shuwei\Core\Framework\ShuweiHttpException;
use Symfony\Component\HttpFoundation\Response;

#[Package('core')]
class InvalidSalesChannelIdException extends ShuweiHttpException
{
    public function __construct(string $salesChannelId)
    {
        parent::__construct(
            'The provided salesChannelId "{{ salesChannelId }}" is invalid.',
            ['salesChannelId' => $salesChannelId]
        );
    }

    public function getErrorCode(): string
    {
        return 'FRAMEWORK__INVALID_SALES_CHANNEL';
    }

    public function getStatusCode(): int
    {
        return Response::HTTP_BAD_REQUEST;
    }
}
