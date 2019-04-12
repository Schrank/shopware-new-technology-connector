<?php
declare(strict_types=1);

namespace LizardsAndPumpkins\Connector\Api;

use PHPUnit\Framework\TestCase;

class CallerTest extends TestCase
{
    /**
     * @var Caller
     */
    private $caller;

    protected function setUp(): void
    {
        $this->caller = new Caller();
    }

    public function testCallApi()
    {

    }
}
