<?php declare(strict_types=1);

namespace Shuwei\Core\Framework\Test\TestCaseBase;

use PHPUnit\Framework\TestCase;
use Shuwei\Core\Framework\Event\EventData\MailRecipientStruct;
use Shuwei\Core\Framework\Event\MailAware;
use Shuwei\Core\Framework\Event\ShuweiEvent;
use Shuwei\Core\System\SalesChannel\SalesChannelContext;

trait MailTemplateTestBehaviour
{
    use EventDispatcherBehaviour;

    /**
     * @param class-string<object> $expectedClass
     */
    public static function assertMailEvent(
        string $expectedClass,
        ShuweiEvent $event,
        SalesChannelContext $salesChannelContext
    ): void {
        TestCase::assertInstanceOf($expectedClass, $event);
        TestCase::assertSame($salesChannelContext->getContext(), $event->getContext());
    }

    public static function assertMailRecipientStructEvent(MailRecipientStruct $expectedStruct, MailAware $event): void
    {
        TestCase::assertSame($expectedStruct->getRecipients(), $event->getMailStruct()->getRecipients());
    }

    protected function catchEvent(string $eventName, ?object &$eventResult): void
    {
        $eventDispatcher = $this->getContainer()->get('event_dispatcher');
        $this->addEventListener($eventDispatcher, $eventName, static function ($event) use (&$eventResult): void {
            $eventResult = $event;
        });
    }
}
