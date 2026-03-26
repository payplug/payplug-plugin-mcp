<?php

declare(strict_types=1);

namespace PayplugPluginCore\Tests\Units\Gateways\Payment\StandardPaymentGateway;

use Mockery;
use PayplugPluginCore\Gateways\Payment\StandardPaymentGateway;
use PHPUnit\Framework\TestCase;

abstract class standardPaymentGatewayBase extends TestCase
{
    protected StandardPaymentGateway&\Mockery\MockInterface $gateway;

    public function setUp(): void
    {
        $this->gateway = Mockery::mock(StandardPaymentGateway::class, [])->makePartial();
    }

    protected function tearDown(): void
    {
        Mockery::close();
    }
}
