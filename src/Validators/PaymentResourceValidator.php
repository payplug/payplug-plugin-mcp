<?php

declare(strict_types=1);

namespace PayPlugPluginMcp\Validators;

use Exception;
use Payplug\Resource\Payment;

class PaymentResourceValidator
{
    private const ID_PATTERN = '/^(inst_|pay_)[a-zA-Z0-9]+$/';

    /**
     * @throws Exception
     */
    public function validate(Payment $payment): void
    {
        $this->validateId($payment);
        $this->validateFailure($payment);
    }

    /**
     * @throws Exception
     */
    public function validateId(Payment $payment): void
    {
        $id = $payment->id;

        if (null === $id || !\is_string($id)) {
            throw new Exception('PaymentResourceValidator: id must be a non-null string.');
        }

        if (!preg_match(self::ID_PATTERN, $id)) {
            throw new Exception(
                \sprintf(
                    'PaymentResourceValidator: id ("%s") must start with "inst_" or "pay_" followed by alphanumeric characters.',
                    $id
                )
            );
        }
    }

    /**
     * @throws Exception
     */
    public function validateFailure(Payment $payment): void
    {
        if (null !== $payment->failure) {
            throw new Exception('PaymentResourceValidator: failure must be null.');
        }
    }

    /**
     * @throws Exception
     */
    public function validateIsPaid(Payment $payment): void
    {
        if (!isset($payment->is_paid) || true !== $payment->is_paid) {
            throw new Exception('PaymentResourceValidator: is_paid must be true.');
        }
    }
}
