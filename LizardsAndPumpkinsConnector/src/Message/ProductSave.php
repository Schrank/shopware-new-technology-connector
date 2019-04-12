<?php
declare(strict_types=1);

namespace LizardsAndPumpkins\Connector\Message;

class ProductSave
{
    /**
     * @var array
     */
    private $ids;

    public function __construct(array $ids)
    {
        $this->ids = $ids;
    }

    /**
     * @return array
     */
    public function getIds(): array
    {
        return $this->ids;
    }
}
