<?php

declare(strict_types=1);

namespace PayPlugPluginMcp\Tests\Units\Gateways\Refund\RefundGateway;

use Mockery;
use PayPlugPluginMcp\Gateways\RefundGateway;
use PHPUnit\Framework\TestCase;

abstract class refundGatewayBase extends TestCase
{
    /** @var RefundGateway */
    protected $gateway;

    public function setUp(): void
    {
        $this->gateway = new RefundGateway();
    }

    protected function tearDown(): void
    {
        Mockery::close();
    }
}
