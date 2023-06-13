<?php declare(strict_types=1);

namespace Shuwei\Core\Framework\Api\Response;

use Shuwei\Core\Framework\Log\Package;
use Symfony\Component\HttpFoundation\JsonResponse;

#[Package('core')]
class JsonApiResponse extends JsonResponse
{
    protected function update(): static
    {
        parent::update();

        $this->headers->set('Content-Type', 'application/vnd.api+json');

        return $this;
    }
}
