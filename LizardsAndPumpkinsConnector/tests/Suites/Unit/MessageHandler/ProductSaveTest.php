<?php
declare(strict_types=1);

namespace LizardsAndPumpkins\Connector\MessageHandler;

use InvalidArgumentException;
use LizardsAndPumpkins\Connector\Api\Caller;
use LizardsAndPumpkins\Connector\Message\ProductSave as ProductSaveMessage;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Shopware\Core\Content\Product\ProductEntity;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityCollection;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\EntitySearchResult;
use Shopware\Core\Framework\Test\MessageQueue\fixtures\TestMessage;

class ProductSaveTest extends TestCase
{
    /**
     * @var ProductSave
     */
    private $handler;

    /**
     * @var MockObject|EntityRepository
     */
    private $repository;

    /**
     * @var MockObject|Caller
     */
    private $caller;

    protected function setUp(): void
    {
        $this->repository = $this->createMock(EntityRepository::class);
        $this->caller = $this->createMock(Caller::class);
        $this->handler = new ProductSave($this->repository, $this->caller);
    }

    public function testGetHandledMessages(): void
    {
        $this->assertContains(
            ProductSaveMessage::class,
            ProductSave::getHandledMessages());
    }

    public function testHandleThrowsExceptionOnWrongMessageType(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid Message type.');
        $this->handler->handle(new TestMessage());
    }

    public function testHandleCallsApi(): void
    {
        $message = $this->createMock(ProductSaveMessage::class);
        $productIds = [1, 2];
        $message->method('getIds')->willReturn($productIds);

        $prod1 = $this->createProductMock();
        $prod2 = $this->createProductMock();

        $collection = new EntityCollection([$prod1, $prod2]);

        $entitySearchResult = new EntitySearchResult(
            1,
            $collection,
            null,
            new Criteria(),
            Context::createDefaultContext()
        );
        $this->repository
            ->expects($this->once())
            ->method('search')
            ->with($this->callback(function (Criteria $criteria) use ($productIds) {
                foreach ($productIds as $productId) {
                    if (! in_array($productId, $criteria->getIds(), true)) {
                        return false;
                    }
                }

                return true;
            }))
            ->willReturn($entitySearchResult);

        $this->caller->expects($this->exactly(2))->method('call')->withConsecutive($prod1, $prod2);

        $this->handler->handle($message);
    }

    /**
     * @return MockObject|ProductEntity
     */
    private function createProductMock(): ProductEntity
    {
        $product = $this->createMock(ProductEntity::class);
        $product->method('getUniqueIdentifier')->willReturn(uniqid('', true));

        return $product;
    }
}

