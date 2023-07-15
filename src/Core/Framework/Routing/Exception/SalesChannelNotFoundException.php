<?php declare(strict_types=1);

namespace Shuwei\Core\Framework\Routing\Exception;

use Shuwei\Core\Framework\Log\Package;
use Shuwei\Core\Framework\ShuweiHttpException;
use Symfony\Component\HttpFoundation\Response;

#[Package('core')]
class WebsiteNotFoundException extends ShuweiHttpException
{
    public function __construct()
    {
        parent::__construct('No matching sales channel found.');
    }

    public function getErrorCode(): string
    {
        return 'FRAMEWORK__ROUTING_SALES_CHANNEL_NOT_FOUND';
    }

    public function getStatusCode(): int
    {
        return Response::HTTP_PRECONDITION_FAILED;
    }
}
