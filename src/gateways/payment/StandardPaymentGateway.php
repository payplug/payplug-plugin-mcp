<?php

namespace PayplugPluginCore\Gateways\Payment;

use PayplugPluginCore\Gateways\PaymentGateway;
use PayplugPluginCore\Models\Entities\PaymentInputDTO;

class StandardPaymentGateway extends PaymentGateway
{
    public function __construct()
    {
        $this->id = 'standard';
        $this->expected_context = [
            'is_deferred',
            'is_integrated',
            'is_guest',
        ];
    }

    /**
     * @param PaymentInputDTO $payment_inputDTO
     * @return array
     * @throws \Exception
     */
    public function formatPaymentAttributes(PaymentInputDTO $payment_inputDTO): array
    {
        $attributes = $this->getDefaultAttributeFromDTO($payment_inputDTO);
        if (empty($attributes)) {
            throw new \Exception('Can\'t generate default payment attributes');
        }

        // todo : Set validator to check this point
        // Vérification des clés attendues dans $context
        $context = $payment_inputDTO->getContext();
        foreach ($this->expected_context as $key) {
            if (!array_key_exists($key, $context)) {
                throw new \Exception('Resource attribe can\'t be formated, excepted parameter " ' . $key . '" is missing.');
            }
        }

        // Update if deferred payment is enable
        if (isset($context['is_deferred']) && $context['is_deferred']) {
            $attributes['authorized_amount'] = $attributes['amount'];
            unset($attributes['amount']);
        }

        // Update if current display is integrated
        if (isset($context['is_integrated']) && $context['is_integrated']) {
            $attributes['integration'] = 'INTEGRATED_PAYMENT';
            unset($attributes['hosted_payment']['cancel_url']);
        }

        // Update payment card could be saved
        $is_guest = isset($context['is_guest']) && $context['is_guest'];
        $attributes['allow_save_card'] = !(bool)$is_guest;

        return $attributes;
    }
}