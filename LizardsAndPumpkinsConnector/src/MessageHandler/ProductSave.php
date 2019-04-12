<?php
declare(strict_types=1);

namespace LizardsAndPumpkins\Connector\MessageHandler;

use LizardsAndPumpkins\Connector\Api\Caller;
use LizardsAndPumpkins\Connector\Message\ProductSave as ProductSaveMessage;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\MessageQueue\Handler\AbstractMessageHandler;

class ProductSave extends AbstractMessageHandler
{
    /**
     * @var EntityRepository
     */
    private $productRepository;
    /**
     * @var Caller
     */
    private $caller;

    public function __construct(EntityRepository $productRepository, Caller $caller)
    {
        $this->productRepository = $productRepository;
        $this->caller = $caller;
    }

    public function handle($message): void
    {
        if (! $message instanceof ProductSaveMessage) {
            throw new \InvalidArgumentException('Invalid Message type.');
        }
        $products = $this->productRepository->search(
            new Criteria($message->getIds()),
            Context::createDefaultContext()
        );

        foreach ($products as $product) {
            $this->caller->call($product);
        }
    }

    public static function getHandledMessages(): iterable
    {
        return [ProductSaveMessage::class];
    }
}
