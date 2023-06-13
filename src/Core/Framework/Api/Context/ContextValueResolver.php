<?php declare(strict_types=1);

namespace Shuwei\Core\Framework\Api\Context;

use Shuwei\Core\Framework\Context;
use Shuwei\Core\Framework\Log\Package;
use Shuwei\Core\PlatformRequest;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;

#[Package('core')]
class ContextValueResolver implements ValueResolverInterface
{
    public function resolve(Request $request, ArgumentMetadata $argument): \Generator
    {
        if ($argument->getType() !== Context::class) {
            return;
        }

        yield $request->attributes->get(PlatformRequest::ATTRIBUTE_CONTEXT_OBJECT);
    }
}
