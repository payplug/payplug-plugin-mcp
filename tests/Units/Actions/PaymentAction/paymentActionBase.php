<?php

declare(strict_types=1);

namespace PayPlugPluginMcp\Tests\Units\Actions\PaymentAction;

use Mockery;
use PayPlugPluginMcp\Actions\PaymentAction;
use PHPUnit\Framework\TestCase;

abstract class paymentActionBase extends TestCase
{
    /** @var PaymentAction&\Mockery\MockInterface */
    protected $action;

    public function setUp(): void
    {
        $this->action = Mockery::mock(PaymentAction::class, [])->makePartial();
    }

    protected function tearDown(): void
    {
        Mockery::close();
    }
}
