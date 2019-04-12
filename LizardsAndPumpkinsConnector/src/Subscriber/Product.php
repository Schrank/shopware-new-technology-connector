<?php
declare(strict_types=1);

namespace LizardsAndPumpkins\Connector\Subscriber;

use LizardsAndPumpkins\Connector\Message\ProductSave;
use Shopware\Core\Content\Product\ProductEvents;
use Shopware\Core\Framework\DataAbstractionLayer\Event\EntityWrittenEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;

class Product implements EventSubscriberInterface
{
    /**
     * @var MessageBusInterface
     */
    private $queue;

    public function __construct(MessageBusInterface $queue)
    {
        $this->queue = $queue;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            ProductEvents::PRODUCT_WRITTEN_EVENT => 'afterWrite',
        ];
    }

    public function afterWrite(EntityWrittenEvent $event): void
    {
        $message = new ProductSave($event->getIds());
        $envelope = new Envelope($message);

        $this->queue->dispatch($envelope);
    }
}
