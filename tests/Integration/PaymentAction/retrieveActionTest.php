<?php

declare(strict_types=1);

namespace PayPlugPluginMcp\Tests\Integration\PaymentAction;

use Mockery;
use Mockery\MockInterface;
use PayPlugPluginMcp\Actions\PaymentAction;
use PayPlugPluginMcp\Tests\Mock\PaymentMock;
use PayPlugPluginMcp\Tests\Mock\PaymentOutputDTOMock;
use PHPUnit\Framework\TestCase;

/**
 * @group integration
 */
class retrieveActionTest extends TestCase
{
    /** @var PaymentAction&MockInterface */
    private $action;

    /** @var MockInterface */
    private $payment_api;

    public function setUp(): void
    {
        $this->action = Mockery::mock(PaymentAction::class, [])->makePartial();
        $this->payment_api = \Mockery::mock('alias:Payplug\Payment');
    }

    public function tearDown(): void
    {
        Mockery::close();
    }

    public function testWhenResourceCantBeRetrieved(): void
    {
        $error_msg  = 'An error occurred during the process';
        $error_code = 500;
        $this->payment_api
            ->shouldReceive('retrieve')
            ->once()
            ->andThrow(new \Exception($error_msg, $error_code));

        $error_output_props = [
            'code'     => $error_code,
            'message'  => $error_msg,
            'result'   => false,
            'resource' => null,
        ];
        $this->assertEquals(
            PaymentOutputDTOMock::get($error_output_props),
            $this->action->retrieveAction('pay_5iHMDxy4ABR4YBVW4UscIn', 'sk_test_bearer_token')
        );
    }

    public function testWhenResourceIsRetrieved(): void
    {
        $resource = PaymentMock::getStandard(['is_paid' => true]);
        $this->payment_api
            ->shouldReceive('retrieve')
            ->once()
            ->andReturn($resource);

        $this->assertEquals(
            PaymentOutputDTOMock::get(['resource' => $resource]),
            $this->action->retrieveAction('pay_5iHMDxy4ABR4YBVW4UscIn', 'sk_test_bearer_token')
        );
    }
}
