<?php

declare(strict_types=1);

namespace PayplugPluginCore\Tests\Units\Actions\PaymentAction;

use PayplugPluginCore\Tests\Mock\PaymentInputDTOMock;
use PayplugPluginCore\Tests\Mock\PaymentMock;
use PayplugPluginCore\Tests\Mock\PaymentOutputDTOMock;

/**
 * @group unit
 * @group action
 * @group payment_action
 */
class createActionTest extends paymentActionBase
{
    private $api_service;
    private $input_dto;
    private $service_loader;
    private $gateway_loader;
    private $payment_gateway;
    private $payment_attributes;

    public function setUp(): void
    {
        parent::setUp();
        $this->input_dto = PaymentInputDTOMock::get([]);

        $this->gateway_loader = \Mockery::mock('GatewayLoader');
        $this->action->shouldReceive('get_gateway')
            ->with('payment')
            ->andReturn($this->gateway_loader);

        $this->service_loader = \Mockery::mock('ServiceLoader');
        $this->action->shouldReceive('get_service')
            ->with('api')
            ->andReturn($this->service_loader);

        $this->api_service = \Mockery::mock('ApiService');
        $this->payment_gateway = \Mockery::mock('PaymentGateway');

        $this->payment_attributes = [
            'amount' => $this->input_dto->getAmount(),
            'currency' => $this->input_dto->getCurrencyIsoCode(),
            'billing' => $this->input_dto->getCustomer()['billing'],
            'shipping' => $this->input_dto->getCustomer()['shipping'] + ['delivery_type' => 'BILLING'],
            'hosted_payment' => [
                'return_url' => $this->input_dto->getUrls()['return'],
                'cancel_url' => $this->input_dto->getUrls()['cancel'],
            ],
            'notification_url' => $this->input_dto->getUrls()['notification'],
            'metadata' => $this->input_dto->getMetadata(),
            'allow_save_card' => false,
            'force_3ds' => false,
        ];
    }

    public function testWhenGivenDTOIsInvalid(): void
    {
        $this->expectException(\TypeError::class);
        $this->action->createAction(null);
    }

    public function testWhenPaymentGatewayLoadingThrowsException(): void
    {
        $exception_msg = 'Payment Gateway can\'t be found.';
        $this->gateway_loader
            ->shouldReceive('load')
            ->once()
            ->with($this->input_dto->getPaymentMethod())
            ->andThrow(new \Exception($exception_msg));

        // Le flux doit s'arrêter avant tout appel API
        $this->action->shouldNotReceive('get_service');

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage($exception_msg);

        $this->action->createAction($this->input_dto);
    }

    public function testWhenContextInDTOIsMissing(): void
    {
        $this->gateway_loader
            ->shouldReceive('load')
            ->once()
            ->with($this->input_dto->getPaymentMethod())
            ->andReturn($this->payment_gateway);

        $exception_msg = 'Resource attribe can\'t be formated, excepted parameter is missing.';
        $this->payment_gateway
            ->shouldReceive('formatPaymentAttributes')
            ->once()
            ->andThrow(new \Exception($exception_msg));

        // Le flux doit s'arrêter avant tout appel API
        $this->action->shouldNotReceive('get_service');

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage($exception_msg);

        $this->action->createAction($this->input_dto);
    }

    public function testWhenApiServiceLoadingThrowsException(): void
    {
        $this->gateway_loader
            ->shouldReceive('load')
            ->once()
            ->with($this->input_dto->getPaymentMethod())
            ->andReturn($this->payment_gateway);

        $this->payment_gateway
            ->shouldReceive('formatPaymentAttributes')
            ->once()
            ->andReturn($this->payment_attributes);

        $exception_msg = 'Payplug API can\'t be initialized.';
        $this->service_loader
            ->shouldReceive('load')
            ->once()
            ->with($this->input_dto->getApiBearer())
            ->andThrow(new \Exception($exception_msg));

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage($exception_msg);

        $this->action->createAction($this->input_dto);
    }

    public function testWhenPaymentResourceCantBeCreated(): void
    {
        $this->gateway_loader
            ->shouldReceive('load')
            ->once()
            ->with($this->input_dto->getPaymentMethod())
            ->andReturn($this->payment_gateway);

        $this->payment_gateway
            ->shouldReceive('formatPaymentAttributes')
            ->once()
            ->andReturn($this->payment_attributes);

        $this->service_loader
            ->shouldReceive('load')
            ->once()
            ->with($this->input_dto->getApiBearer())
            ->andReturn($this->api_service);

        $error_code = 500;
        $error_msg = 'An error occurred during payment creation.';
        $api_return = [
            'code' => $error_code,
            'message' => $error_msg,
            'response' => null,
            'result' => false,
        ];
        $this->api_service
            ->shouldReceive('createPaymentResource')
            ->once()
            ->andReturn($api_return);
        $this->assertEquals(
            PaymentOutputDTOMock::get($api_return),
            $this->action->createAction($this->input_dto)
        );
    }

    public function testWhenPaymentOutputDTOIsReturnWithSuccess(): void
    {
        $this->gateway_loader
            ->shouldReceive('load')
            ->once()
            ->with($this->input_dto->getPaymentMethod())
            ->andReturn($this->payment_gateway);

        $this->payment_gateway
            ->shouldReceive('formatPaymentAttributes')
            ->once()
            ->andReturn($this->payment_attributes);

        $this->service_loader
            ->shouldReceive('load')
            ->once()
            ->with($this->input_dto->getApiBearer())
            ->andReturn($this->api_service);

        $error_code = 500;
        $error_msg = 'An error occurred during payment creation.';
        $api_return = [
            'code' => 200,
            'message' => 'OK',
            'response' => PaymentMock::getStandard(),
            'result' => true,
        ];
        $this->api_service
            ->shouldReceive('createPaymentResource')
            ->once()
            ->andReturn($api_return);
        $this->assertEquals(
            PaymentOutputDTOMock::get([
                'response' => PaymentMock::getStandard(),
            ]),
            $this->action->createAction($this->input_dto)
        );
    }
}
