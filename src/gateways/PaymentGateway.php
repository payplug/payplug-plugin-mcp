<?php

namespace PayplugPluginCore\Gateways;

use PayplugPluginCore\Models\Entities\PaymentInputDTO;

class PaymentGateway
{
    /** @var string */
    protected $id;

    /** @var array */
    protected $expected_context;

    /**
     * @param string $payment_gateway_name
     * @return PaymentGateway
     * @throws \Exception
     */
    public function load(string $payment_gateway_name): object
    {
        if (empty($payment_gateway_name)) {
            throw new \Exception('Invalid parameter, $payment_gateway_name given should be a non empty string.');
        }

        $payment_gateway_path = '\PayplugPluginCore\gateways\payment\\'
            . str_replace('_', '', ucwords($payment_gateway_name, '_'))
            . 'PaymentGateway';
        if (!class_exists($payment_gateway_path)) {
            throw new \Exception('Payment Gateway can\'t be found.');
        }
        $payment_gateway = new $payment_gateway_path();

        return $payment_gateway;
    }

    /**
     * @param PaymentInputDTO $payment_inputDTO
     * @return array
     */
    public function getDefaultAttributeFromDTO(PaymentInputDTO $payment_inputDTO): array
    {
        if (empty($payment_inputDTO)) {
            throw new \Exception('Invalid parameter, $payment_inputDTO given should be a non empty object.');
        }

        return [
            'amount' => $payment_inputDTO->getAmount(),
            'currency' => $payment_inputDTO->getCurrency(),
            'billing' => $payment_inputDTO->getCustomer()['billing'],
            'shipping' => $payment_inputDTO->getCustomer()['shipping'] + ['delivery_type' => 'BILLING'],
            'hosted_payment' => [
                'return_url' => $payment_inputDTO->getUrls()['return'],
                'cancel_url' => $payment_inputDTO->getUrls()['cancel'],
            ],
            'notification_url' => $payment_inputDTO->getUrls()['notification'],
            'metadata' => $payment_inputDTO->getMetadata(),
            'allow_save_card' => false,
            'force_3ds' => false,
        ];
    }
}