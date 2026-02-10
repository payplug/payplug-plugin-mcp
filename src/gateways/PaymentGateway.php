<?php

namespace PayplugPluginCore\Gateways;

class PaymentGateway
{
    /** @var string */
    protected $id;

    /** @var array */
    protected $expected_context;

    /**
     * @param string $payment_gateway_name
     * @return mixed
     * @throws \Exception
     */
    public function load($payment_gateway_name = '')
    {
        if(!is_string($payment_gateway_name) || empty($payment_gateway_name)) {
            throw new \Exception('Invalid parameter, $payment_gateway_name given should be a non empty string.');
        }

        $payment_gateway_path = '\PayplugPluginCore\gateways\payment\\'
            . str_replace('_', '', ucwords($payment_gateway_name, '_'))
            . 'Gateway';
        if (!class_exists($payment_gateway_path)) {
            throw new \Exception('Payment Gateway can\'t be found.');
        }
        $payment_gateway = new $payment_gateway_path();

        return $payment_gateway;
    }
}