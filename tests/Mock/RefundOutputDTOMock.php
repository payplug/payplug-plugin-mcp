<?php

declare(strict_types=1);

namespace PayPlugPluginCore\Tests\Mock;

use Payplug\Resource\Refund;
use PayPlugPluginCore\Models\Entities\RefundOutputDTO;

class RefundOutputDTOMock
{
    /**
     * @param array<string, mixed>|null $custom_props
     * @throws \Exception
     */
    public static function get(?array $custom_props): RefundOutputDTO
    {
        $props = [
            'result'   => true,
            'code'     => 200,
            'message'  => 'OK',
            'resource' => Refund::fromAttributes([
                'id'         => 're_azerty',
                'object'     => 'refund',
                'is_live'    => true,
                'amount'     => 1000,
                'currency'   => 'EUR',
                'created_at' => time(),
                'payment_id' => 'pay_azerty',
                'metadata'   => null,
            ]),
        ];

        if ($custom_props !== null) {
            foreach ($custom_props as $prop => $value) {
                $props[$prop] = $value;
            }
        }

        $refundOutputDTO = new RefundOutputDTO();
        $result = $refundOutputDTO->hydrate($props);
        if ($result === null) {
            throw new \RuntimeException('RefundOutputDTOMock failed to hydrate DTO.');
        }

        return $result;
    }
}
