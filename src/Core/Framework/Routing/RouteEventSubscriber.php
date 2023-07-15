<?php declare(strict_types=1);

namespace Shuwei\Core\Framework\Routing;

use Shuwei\Core\Framework\Log\Package;
use Shuwei\Frontend\Event\FrontendRenderEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

/**
 * @internal
 */
#[Package('core')]
class RouteEventSubscriber implements EventSubscriberInterface
{
    /**
     * @internal
     */
    public function __construct(private readonly EventDispatcherInterface $dispatcher)
    {
    }

    public static function getSubscribedEvents(): array
    {
        $events = [
            KernelEvents::REQUEST => ['request', -10],
            KernelEvents::RESPONSE => ['response', -10],
        ];

        if (class_exists(FrontendRenderEvent::class)) {
            $events[FrontendRenderEvent::class] = ['render', -10];
        }

        return $events;
    }

    public function request(RequestEvent $event): void
    {
        $request = $event->getRequest();
        if (!$request->attributes->has('_route')) {
            return;
        }

        $name = $request->attributes->get('_route') . '.request';
        $this->dispatcher->dispatch($event, $name);
    }

    public function render(FrontendRenderEvent $event): void
    {
        $request = $event->getRequest();
        if (!$request->attributes->has('_route')) {
            return;
        }

        $name = $request->attributes->get('_route') . '.render';
        $this->dispatcher->dispatch($event, $name);
    }

    public function response(ResponseEvent $event): void
    {
        $request = $event->getRequest();
        if (!$request->attributes->has('_route')) {
            return;
        }

        $name = $request->attributes->get('_route') . '.response';
        $this->dispatcher->dispatch($event, $name);
    }
}
