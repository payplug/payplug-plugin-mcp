<?php

declare(strict_types=1);

namespace PayPlugPluginCore\Tests\Units\Gateways\Payment\StandardPaymentGateway;

use Mockery;
use PayPlugPluginCore\Gateways\Payment\StandardPaymentGateway;
use PHPUnit\Framework\TestCase;

abstract class standardPaymentGatewayBase extends TestCase
{
    /** @var StandardPaymentGateway&\Mockery\MockInterface */
    protected $gateway;

    public function setUp(): void
    {
        $this->gateway = Mockery::mock(StandardPaymentGateway::class, [])->makePartial();
    }

    protected function tearDown(): void
    {
        Mockery::close();
    }
}
