<?php

declare(strict_types=1);

namespace PayplugPluginCore\Tests\Integration\PaymentAction;

use Mockery;
use PayplugPluginCore\Actions\PaymentAction;
use PayplugPluginCore\Tests\Mock\PaymentInputDTOMock;
use PayplugPluginCore\Tests\Mock\PaymentMock;
use PayplugPluginCore\Tests\Mock\PaymentOutputDTOMock;
use PHPUnit\Framework\TestCase;

/**
 * @group integration
 */
class createActionTest extends TestCase
{
    private $action;
    private $payment_api;

    public function setUp(): void
    {
        $this->action = Mockery::mock(PaymentAction::class, [])->makePartial();
        $this->payment_api = \Mockery::mock('alias:Payplug\Payment');
        $this->default_payment_input_DTO = PaymentInputDTOMock::get([
            'payment_method' => 'standard',
            'context' => [
                'is_deferred' => false,
                'is_integrated' => false,
                'is_guest' => false,
            ],
        ]);
    }

    public function tearDown(): void
    {
        Mockery::close();
    }

    public function testWhenResourceCantBeCreated(): void
    {
        $error_msg = 'An error occured during the process';
        $error_code = 500;
        $this->payment_api
            ->shouldReceive('create')
            ->once()
            ->andThrow(new \Exception($error_msg, $error_code));

        $error_output_props = [
            'code' => $error_code,
            'message' => $error_msg,
            'result' => false,
            'response' => null,
        ];
        $this->assertEquals(
            PaymentOutputDTOMock::get($error_output_props),
            $this->action->createAction($this->default_payment_input_DTO)
        );
    }

    public function testWhenResourceIsCreated(): void
    {

        $resource = PaymentMock::getStandard();
        $this->payment_api
            ->shouldReceive('create')
            ->once()
            ->andReturn($resource);

        $success_output_props = [
            'response' => $resource,
        ];
        $payment_output_DTO = PaymentOutputDTOMock::get($success_output_props);
        $return = $this->action->createAction($this->default_payment_input_DTO);

        $this->assertEquals($payment_output_DTO, $return);
    }
}
