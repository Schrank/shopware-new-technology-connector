<?php
declare(strict_types=1);

namespace LizardsAndPumpkins\Connector\Subscriber;

use Closure;
use LizardsAndPumpkins\Connector\Message\ProductSave;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Shopware\Core\Content\Product\ProductEvents;
use Shopware\Core\Framework\DataAbstractionLayer\Event\EntityWrittenEvent;
use Shopware\Core\Framework\DataAbstractionLayer\Write\Command\WriteCommandQueue;
use Shopware\Core\Framework\Test\MessageQueue\fixtures\TestMessage;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;

class ProductTest extends TestCase
{
    /**
     * @var Product
     */
    private $subscriber;

    /**
     * @var MockObject|WriteCommandQueue
     */
    private $queueMock;

    protected function setUp(): void
    {
        $this->queueMock = $this->createMock(MessageBusInterface::class);
        $this->subscriber = new Product($this->queueMock);
    }

    public function testSubscribedEvents(): void
    {
        $events = [
            ProductEvents::PRODUCT_WRITTEN_EVENT,
        ];

        foreach ($events as $event) {
            $this->assertArrayHasKey($event, Product::getSubscribedEvents());
            $this->assertSame('afterWrite', Product::getSubscribedEvents()[$event]);
        }
    }

    public function testWritesUpdateCommandOnQueue(): void
    {
        $event = $this->createMock(EntityWrittenEvent::class);
        $productIds = [4711, 1337];
        $event->method('getIds')->willReturn($productIds);

        $expected = $productIds;
        $this->queueMock->expects($this->once())->method('dispatch')->with(
            $this->callback(
                $this->envelopeContainsAllExpectedProductIds($expected)
            )
        )->willReturn(new Envelope(new TestMessage()));

        $this->subscriber->afterWrite($event);
    }

    private function envelopeContainsAllExpectedProductIds(array $expected): Closure
    {
        return static function (Envelope $envelope) use ($expected) {
            /** @var ProductSave $message */
            $message = $envelope->getMessage();
            $productIds = $message->getIds();
            foreach ($expected as $id) {
                if (! in_array($id, $productIds, true)) {
                    return false;
                }
            }

            return true;
        };
    }

}
