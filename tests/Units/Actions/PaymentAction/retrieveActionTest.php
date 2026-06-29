<?php

declare(strict_types=1);

namespace PayPlugPluginMcp\Tests\Units\Actions\PaymentAction;

use Mockery\MockInterface;
use PayPlugPluginMcp\Tests\Mock\PaymentMock;
use PayPlugPluginMcp\Tests\Mock\PaymentOutputDTOMock;
use PayPlugPluginMcp\Utilities\Services\Api;

/**
 * @group units
 * @group action
 * @group retrieve_action
 */
class retrieveActionTest extends paymentActionBase
{
    private const RESOURCE_ID = 'pay_5iHMDxy4ABR4YBVW4UscIn';
    private const API_BEARER  = 'sk_test_bearer_token';

    /** @var MockInterface */
    private $api;

    /** @var MockInterface */
    private $api_service;

    public function setUp(): void
    {
        parent::setUp();

        $this->api = \Mockery::mock(Api::class);
        $this->action->shouldReceive('get_api')
            ->andReturn($this->api);

        $this->api_service = \Mockery::mock(Api::class);
    }

    public function testWhenApiServiceLoadingThrowsException(): void
    {
        $exception_msg = 'Payplug API can\'t be initialized.';
        $this->api
            ->shouldReceive('load')
            ->once()
            ->with(self::API_BEARER)
            ->andThrow(new \Exception($exception_msg));

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage($exception_msg);
        $this->action->retrieveAction(self::RESOURCE_ID, self::API_BEARER);
    }

    public function testWhenPaymentResourceCantBeRetrieved(): void
    {
        $this->api
            ->shouldReceive('load')
            ->once()
            ->with(self::API_BEARER)
            ->andReturn($this->api_service);

        $api_return = [
            'code'     => 404,
            'message'  => 'Payment not found.',
            'resource' => null,
            'result'   => false,
        ];
        $this->api_service
            ->shouldReceive('retrievePaymentResource')
            ->once()
            ->with(self::RESOURCE_ID)
            ->andReturn($api_return);

        $this->assertEquals(
            PaymentOutputDTOMock::get($api_return),
            $this->action->retrieveAction(self::RESOURCE_ID, self::API_BEARER)
        );
    }

    public function testWhenPaymentOutputDTOIsReturnedWithSuccess(): void
    {
        $this->api
            ->shouldReceive('load')
            ->once()
            ->with(self::API_BEARER)
            ->andReturn($this->api_service);

        $payment = PaymentMock::getStandard(['is_paid' => true]);
        $api_return = [
            'code'     => 200,
            'message'  => 'OK',
            'resource' => $payment,
            'result'   => true,
        ];
        $this->api_service
            ->shouldReceive('retrievePaymentResource')
            ->once()
            ->with(self::RESOURCE_ID)
            ->andReturn($api_return);

        $this->assertEquals(
            PaymentOutputDTOMock::get(['resource' => $payment]),
            $this->action->retrieveAction(self::RESOURCE_ID, self::API_BEARER)
        );
    }
}
