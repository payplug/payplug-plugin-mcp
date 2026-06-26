<?php

declare(strict_types=1);

namespace PayPlugPluginMcp\Tests\Integration\PaymentAction;

use Mockery;
use Mockery\MockInterface;
use PayPlugPluginMcp\Actions\PaymentAction;
use PayPlugPluginMcp\Models\Entities\RefundInputDTO;
use PayPlugPluginMcp\Tests\Mock\RefundInputDTOMock;
use PayPlugPluginMcp\Tests\Mock\RefundOutputDTOMock;
use PHPUnit\Framework\TestCase;

/**
 * @group integration
 */
class refundActionTest extends TestCase
{
    /** @var PaymentAction&MockInterface */
    private $action;
    /** @var MockInterface */
    private $refund_api;
    /** @var RefundInputDTO */
    private $default_refund_input_dto;

    public function setUp(): void
    {
        $this->action = Mockery::mock(PaymentAction::class, [])->makePartial();
        $this->refund_api = \Mockery::mock('alias:Payplug\Refund');
        $this->default_refund_input_dto = RefundInputDTOMock::get([]);
    }

    public function tearDown(): void
    {
        Mockery::close();
    }

    public function testWhenRefundCantBeCreated(): void
    {
        $error_msg = 'An error occurred during the process';
        $error_code = 500;
        $this->refund_api
            ->shouldReceive('create')
            ->once()
            ->andThrow(new \Exception($error_msg, $error_code));

        $error_output_props = [
            'code'     => $error_code,
            'message'  => $error_msg,
            'result'   => false,
            'resource' => null,
        ];
        $this->assertEquals(
            RefundOutputDTOMock::get($error_output_props),
            $this->action->refundAction($this->default_refund_input_dto)
        );
    }

    public function testWhenRefundIsCreated(): void
    {
        $resource = \Payplug\Resource\Refund::fromAttributes([
            'id'         => 're_azerty',
            'object'     => 'refund',
            'is_live'    => true,
            'amount'     => 1000,
            'currency'   => 'EUR',
            'created_at' => time(),
            'payment_id' => 'pay_azerty',
            'metadata'   => null,
        ]);

        $this->refund_api
            ->shouldReceive('create')
            ->once()
            ->andReturn($resource);

        $this->assertEquals(
            RefundOutputDTOMock::get(['resource' => $resource]),
            $this->action->refundAction($this->default_refund_input_dto)
        );
    }
}
