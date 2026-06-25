<?php

declare(strict_types=1);

namespace PayPlugPluginCore\Gateways;

use PayPlugPluginCore\Models\Entities\RefundInputDTO;

class RefundGateway
{
    /**
     * @param RefundInputDTO $refund_inputDTO
     * @return array<string, mixed>
     * @throws \Exception
     */
    public function formatRefundAttributes(RefundInputDTO $refund_inputDTO): array
    {
        $formated_attributes = [
            'amount' => $refund_inputDTO->getAmount(),
            'metadata' => [
                'customer_id' => $refund_inputDTO->getCustomerId(),
                'reason' => $refund_inputDTO->getReason(),
            ],
        ];

        return $formated_attributes;
    }
}
