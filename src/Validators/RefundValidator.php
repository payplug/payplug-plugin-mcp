<?php

declare(strict_types=1);

namespace PayPlugPluginCore\Validators;

use Exception;
use Payplug\Resource\Payment;
use PayPlugPluginCore\Models\Entities\RefundInputDTO;

class RefundValidator
{
    private const AMOUNT_MIN = 10;

    /**
     * Validates that the RefundInputDTO contains coherent and allowed data.
     *
     * @param RefundInputDTO $refund_inputDTO
     * @throws Exception
     */
    public function validate(RefundInputDTO $refund_inputDTO): void
    {
        $this->validateAmount($refund_inputDTO);
        $this->validateRefundableWindow($refund_inputDTO);
    }

    /**
     * Checks that the requested amount is within the allowed range:
     *  - at least AMOUNT_MIN cents
     *  - at most (payment.amount − payment.amount_refunded) cents
     *
     * @throws Exception
     */
    private function validateAmount(RefundInputDTO $refund_inputDTO): void
    {
        $amount   = $refund_inputDTO->getAmount();
        $resource = $refund_inputDTO->getResource();

        if (null === $amount) {
            throw new Exception('RefundValidator: amount is required.');
        }

        if ($amount < self::AMOUNT_MIN) {
            throw new Exception(
                sprintf(
                    'RefundValidator: amount (%d) must be at least %d cents.',
                    $amount,
                    self::AMOUNT_MIN
                )
            );
        }

        if ($resource instanceof Payment) {
            $refundable_limit = $resource->amount - $resource->amount_refunded;

            if ($amount > $refundable_limit) {
                throw new Exception(
                    sprintf(
                        'RefundValidator: amount (%d) exceeds the refundable limit (%d cents).',
                        $amount,
                        $refundable_limit
                    )
                );
            }
        }
    }

    /**
     * Checks that the current date/time falls within the payment's refundable window:
     *  - after refundable_after (when defined)
     *  - before refundable_until (when defined)
     *
     * @throws Exception
     */
    private function validateRefundableWindow(RefundInputDTO $refund_inputDTO): void
    {
        $resource = $refund_inputDTO->getResource();

        if (!$resource instanceof Payment) {
            return;
        }

        $now = time();

        if (null !== $resource->refundable_after && $now < (int) $resource->refundable_after) {
            throw new Exception(
                sprintf(
                    'RefundValidator: refund is not yet available (refundable_after: %s).',
                    date('Y-m-d H:i:s', (int) $resource->refundable_after)
                )
            );
        }

        if (null !== $resource->refundable_until && $now > (int) $resource->refundable_until) {
            throw new Exception(
                sprintf(
                    'RefundValidator: refund period has expired (refundable_until: %s).',
                    date('Y-m-d H:i:s', (int) $resource->refundable_until)
                )
            );
        }
    }
}
