<?php

declare(strict_types=1);

namespace PayPlugPluginCore\Tests\Mock;

use PayPlugPluginCore\Models\Entities\RefundInputDTO;

class RefundInputDTOMock
{
    /**
     * @param array<string, mixed>|null $custom_props
     * @throws \Exception
     */
    public static function get(?array $custom_props): RefundInputDTO
    {
        $props = [
            'api_bearer'  => 'token_bearer',
            'resource'    => PaymentMock::getStandard(['is_paid' => true]),
            'amount'      => 1000,
            'customer_id' => 42,
            'reason'      => 'test reason',
        ];

        if (!empty($custom_props)) {
            foreach ($custom_props as $prop => $value) {
                $props[$prop] = $value;
            }
        }

        $refundInputDTO = new RefundInputDTO();
        $result = $refundInputDTO->hydrate($props);
        if ($result === null) {
            throw new \RuntimeException('RefundInputDTOMock failed to hydrate DTO.');
        }

        return $result;
    }
}
