<?php

declare(strict_types=1);

namespace PayPlugPluginCore\Tests\Units\Gateways\Refund\RefundGateway;

use PayPlugPluginCore\Tests\Mock\RefundInputDTOMock;

/**
 * @group units
 * @group gateways
 * @group refund_gateway
 */
class formatRefundAttributesTest extends refundGatewayBase
{
    /**
     * @throws \ReflectionException
     */
    public function testWhenGivenDTOIsInvalid(): void
    {
        $this->expectException(\TypeError::class);
        (new \ReflectionMethod($this->gateway, 'formatRefundAttributes'))->invoke($this->gateway, null);
    }

    public function testWhenAttributesAreFormatted(): void
    {
        $dto = RefundInputDTOMock::get([]);
        $result = $this->gateway->formatRefundAttributes($dto);

        $this->assertEquals([
            'amount'   => $dto->getAmount(),
            'metadata' => [
                'customer_id' => $dto->getCustomerId(),
                'reason'      => $dto->getReason(),
            ],
        ], $result);
    }

    public function testWhenOptionalFieldsAreNull(): void
    {
        $dto = RefundInputDTOMock::get([
            'customer_id' => null,
            'reason'      => null,
        ]);
        $result = $this->gateway->formatRefundAttributes($dto);

        $this->assertEquals([
            'amount'   => $dto->getAmount(),
            'metadata' => [
                'customer_id' => null,
                'reason'      => null,
            ],
        ], $result);
    }
}
