<?php

declare(strict_types=1);

namespace PayPlugPluginCore\Tests\Units\Actions\PaymentAction;

use Mockery\MockInterface;
use PayPlugPluginCore\Gateways\RefundGateway;
use PayPlugPluginCore\Models\Entities\RefundInputDTO;
use PayPlugPluginCore\Tests\Mock\PaymentMock;
use PayPlugPluginCore\Tests\Mock\RefundInputDTOMock;
use PayPlugPluginCore\Tests\Mock\RefundOutputDTOMock;
use PayPlugPluginCore\Utilities\Services\Api;

/**
 * @group units
 * @group action
 * @group refund_action
 */
class refundActionTest extends paymentActionBase
{
    /** @var RefundInputDTO */
    private $input_dto;
    /** @var MockInterface */
    private $api;
    /** @var MockInterface */
    private $api_service;

    /** @var array<string, mixed> */
    private $refund_attributes = [];

    public function setUp(): void
    {
        parent::setUp();
        $this->input_dto = RefundInputDTOMock::get([]);

        $this->action->shouldReceive('get_refund_gateway')
            ->andReturn(new RefundGateway());

        $this->api = \Mockery::mock(Api::class);
        $this->action->shouldReceive('get_api')
            ->andReturn($this->api);

        $this->api_service = \Mockery::mock(Api::class);

        $this->refund_attributes = [
            'amount'   => $this->input_dto->getAmount(),
            'metadata' => [
                'customer_id' => $this->input_dto->getCustomerId(),
                'reason'      => $this->input_dto->getReason(),
            ],
        ];
    }

    public function testWhenGivenDTOIsInvalid(): void
    {
        $this->expectException(\TypeError::class);
        (new \ReflectionMethod($this->action, 'refundAction'))->invoke($this->action, null);
    }

    public function testWhenResourceIsNull(): void
    {
        $this->input_dto->resource = null;

        $this->action->shouldNotReceive('get_refund_gateway');
        $this->action->shouldNotReceive('get_api');

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Invalid parameter, resource is required.');
        $this->action->refundAction($this->input_dto);
    }

    public function testWhenRefundValidatorThrowsException(): void
    {
        $this->input_dto = RefundInputDTOMock::get(['amount' => 5]);

        $this->action->shouldNotReceive('get_refund_gateway');
        $this->action->shouldNotReceive('get_api');

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('RefundValidator: amount (5) must be at least 10 cents.');
        $this->action->refundAction($this->input_dto);
    }

    public function testWhenPaymentResourceIsInvalid(): void
    {
        $this->input_dto = RefundInputDTOMock::get([
            'resource' => PaymentMock::getStandard(['id' => 'bad_id']),
        ]);

        $this->action->shouldNotReceive('get_refund_gateway');
        $this->action->shouldNotReceive('get_api');

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('PaymentResourceValidator: id ("bad_id") must start with "inst_" or "pay_" followed by alphanumeric characters.');
        $this->action->refundAction($this->input_dto);
    }

    public function testWhenPaymentIsNotPaid(): void
    {
        $this->input_dto = RefundInputDTOMock::get([
            'resource' => PaymentMock::getStandard(['is_paid' => false]),
        ]);

        $this->action->shouldNotReceive('get_refund_gateway');
        $this->action->shouldNotReceive('get_api');

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('PaymentResourceValidator: is_paid must be true.');
        $this->action->refundAction($this->input_dto);
    }

    public function testWhenApiServiceLoadingThrowsException(): void
    {
        $exception_msg = 'Payplug API can\'t be initialized.';
        $this->api
            ->shouldReceive('load')
            ->once()
            ->with($this->input_dto->getApiBearer())
            ->andThrow(new \Exception($exception_msg));

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage($exception_msg);
        $this->action->refundAction($this->input_dto);
    }

    public function testWhenRefundResourceCantBeCreated(): void
    {
        $this->api
            ->shouldReceive('load')
            ->once()
            ->with($this->input_dto->getApiBearer())
            ->andReturn($this->api_service);

        $api_return = [
            'code'     => 500,
            'message'  => 'An error occurred during refund creation.',
            'resource' => null,
            'result'   => false,
        ];
        $this->api_service
            ->shouldReceive('refundPaymentResource')
            ->once()
            ->andReturn($api_return);

        $this->assertEquals(
            RefundOutputDTOMock::get($api_return),
            $this->action->refundAction($this->input_dto)
        );
    }

    public function testWhenRefundOutputDTOIsReturnedWithSuccess(): void
    {
        $this->api
            ->shouldReceive('load')
            ->once()
            ->with($this->input_dto->getApiBearer())
            ->andReturn($this->api_service);

        $api_return = [
            'code'     => 200,
            'message'  => 'OK',
            'resource' => null,
            'result'   => true,
        ];
        $this->api_service
            ->shouldReceive('refundPaymentResource')
            ->once()
            ->andReturn($api_return);

        $this->assertEquals(
            RefundOutputDTOMock::get($api_return),
            $this->action->refundAction($this->input_dto)
        );
    }
}
