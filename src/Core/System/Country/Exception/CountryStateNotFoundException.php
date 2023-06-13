<?php declare(strict_types=1);

namespace Shuwei\Core\System\Country\Exception;

use Shuwei\Core\Framework\Log\Package;
use Shuwei\Core\Framework\ShuweiHttpException;
use Symfony\Component\HttpFoundation\Response;

#[Package('system-settings')]
class CountryStateNotFoundException extends ShuweiHttpException
{
    public function __construct(string $id)
    {
        parent::__construct(
            'Country state with id "{{ stateId }}" not found.',
            ['stateId' => $id]
        );
    }

    public function getErrorCode(): string
    {
        return 'CHECKOUT__COUNTRY_STATE_NOT_FOUND';
    }

    public function getStatusCode(): int
    {
        return Response::HTTP_BAD_REQUEST;
    }
}
