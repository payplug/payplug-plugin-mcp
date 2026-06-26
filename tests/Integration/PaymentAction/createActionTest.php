<?php

declare(strict_types=1);

namespace PayPlugPluginMcp\Tests\Integration\PaymentAction;

use Mockery;
use Mockery\MockInterface;
use PayPlugPluginMcp\Actions\PaymentAction;
use PayPlugPluginMcp\Models\Entities\PaymentInputDTO;
use PayPlugPluginMcp\Tests\Mock\PaymentInputDTOMock;
use PayPlugPluginMcp\Tests\Mock\PaymentMock;
use PayPlugPluginMcp\Tests\Mock\PaymentOutputDTOMock;
use PHPUnit\Framework\TestCase;

/**
 * @group integration
 */
class createActionTest extends TestCase
{
    /** @var PaymentAction&MockInterface */
    private $action;
    /** @var MockInterface */
    private $payment_api;
    /** @var PaymentInputDTO */
    private $default_payment_input_DTO;

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
        $error_msg = 'An error occurred during the process';
        $error_code = 500;
        $this->payment_api
            ->shouldReceive('create')
            ->once()
            ->andThrow(new \Exception($error_msg, $error_code));

        $error_output_props = [
            'code' => $error_code,
            'message' => $error_msg,
            'result' => false,
            'resource' => null,
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
            'resource' => $resource,
        ];
        $payment_output_DTO = PaymentOutputDTOMock::get($success_output_props);
        $return = $this->action->createAction($this->default_payment_input_DTO);

        $this->assertEquals($payment_output_DTO, $return);
    }
}
