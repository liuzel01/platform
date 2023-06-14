<?php declare(strict_types=1);

namespace Shuwei\Core\Framework\Api\Acl;

use Doctrine\DBAL\Connection;
use Shuwei\Core\Framework\Api\Exception\MissingPrivilegeException;
use Shuwei\Core\Framework\Log\Package;
use Shuwei\Core\Framework\Routing\KernelListenerPriorities;
use Shuwei\Core\Framework\Uuid\Uuid;
use Shuwei\Core\PlatformRequest;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * @internal
 */
#[Package('core')]
class AclAnnotationValidator implements EventSubscriberInterface
{
    /**
     * @internal
     */
    public function __construct(private readonly Connection $connection)
    {
    }

    /**
     * @return array<string, string|array{0: string, 1: int}|list<array{0: string, 1?: int}>>
     */
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::CONTROLLER => [
                ['validate', KernelListenerPriorities::KERNEL_CONTROLLER_EVENT_SCOPE_VALIDATE],
            ],
        ];
    }

    public function validate(ControllerEvent $event): void
    {
        $request = $event->getRequest();

        $privileges = $request->attributes->get(PlatformRequest::ATTRIBUTE_ACL);

        if (!$privileges) {
            return;
        }

        $context = $request->attributes->get(PlatformRequest::ATTRIBUTE_CONTEXT_OBJECT);
        if ($context === null) {
            throw new MissingPrivilegeException([]);
        }

        foreach ($privileges as $privilege) {
            if (!$context->isAllowed($privilege)) {
                throw new MissingPrivilegeException([$privilege]);
            }
        }
    }
}
