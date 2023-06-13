<?php declare(strict_types=1);

namespace Shuwei\Core\Framework\Test\MessageQueue\Subscriber;

use PHPUnit\Framework\TestCase;
use Shuwei\Core\Framework\Increment\AbstractIncrementer;
use Shuwei\Core\Framework\Increment\IncrementGatewayRegistry;
use Shuwei\Core\Framework\Test\MessageQueue\fixtures\BarMessage;
use Shuwei\Core\Framework\Test\MessageQueue\fixtures\FooMessage;
use Shuwei\Core\Framework\Test\MessageQueue\fixtures\NoHandlerMessage;
use Shuwei\Core\Framework\Test\TestCaseBase\IntegrationTestBehaviour;
use Shuwei\Core\Framework\Test\TestCaseBase\QueueTestBehaviour;
use Symfony\Component\Messenger\MessageBusInterface;

/**
 * @internal
 */
class MessageQueueStatsSubscriberTest extends TestCase
{
    use IntegrationTestBehaviour;
    use QueueTestBehaviour;

    public function testListener(): void
    {
        /** @var AbstractIncrementer $pool */
        $pool = $this->getContainer()
            ->get('shuwei.increment.gateway.registry')
            ->get(IncrementGatewayRegistry::MESSAGE_QUEUE_POOL);

        $pool->reset('message_queue_stats');

        /** @var MessageBusInterface $bus */
        $bus = $this->getContainer()->get('messenger.bus.test_shuwei');

        $bus->dispatch(new FooMessage());
        $bus->dispatch(new BarMessage());
        $bus->dispatch(new BarMessage());
        $bus->dispatch(new BarMessage());

        $stats = $pool->list('message_queue_stats');
        static::assertEquals(1, $stats[FooMessage::class]['count']);
        static::assertEquals(3, $stats[BarMessage::class]['count']);

        $this->runWorker();

        $stats = $pool->list('message_queue_stats');
        static::assertEquals(0, $stats[FooMessage::class]['count']);
        static::assertEquals(0, $stats[BarMessage::class]['count']);

        $bus->dispatch(new NoHandlerMessage());

        $stats = $pool->list('message_queue_stats');
        static::assertEquals(1, $stats[NoHandlerMessage::class]['count']);

        $this->runWorker();
        $stats = $pool->list('message_queue_stats');
        static::assertEquals(0, $stats[NoHandlerMessage::class]['count']);
    }
}
