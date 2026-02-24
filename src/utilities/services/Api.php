<?php

namespace PayplugPluginCore\services;

use Payplug\Payplug;

class Api
{
    /** @var true */
    private $initialized;
    /** @var string */
    private $bearer_token;

    /** @var string */
    private const PAYMENT_CREATE = '\Payplug\Payment::create';

    /**
     * @param array $datas
     * @return array
     */
    public function createPaymentResource(array $datas): array
    {
        return $this->doRequest(self::PAYMENT_CREATE, $datas);
    }

    /**
     * @return string
     */
    public function getBearerToken(): string
    {
        return (string)$this->bearer_token;
    }

    /**
     * @param string $bearer_token
     * @return void
     */
    private function setBearerToken(string $bearer_token): void
    {
        $this->bearer_token = $bearer_token;
    }

    /**
     * @param string $bearer_token
     * @return $this
     * @throws \Payplug\Exception\ConfigurationException
     */
    public function load(string $bearer_token)
    {
        $this->setBearerToken((string)$bearer_token);
        $this->initialize();
        return $this;
    }

    /**
     * @param string $callback
     * @param array $params
     * @return array
     */
    protected function doRequest(string $callback, array $params)
    {
        try {
            $response = [
                'result' => true,
                'response' => call_user_func_array($callback, $params),
                'code' => 200,
            ];
        } catch (\Exception $e) {
            $response = [
                'result' => false,
                'response' => null,
                'code' => $e->getCode(),
            ];
        }

        return $response;
    }

    /**
     * @return void
     * @throws \Payplug\Exception\ConfigurationException
     */
    private function initialize(): void
    {
        try {
            $this->initialized = Payplug::init([
                'secretKey' => $this->getBearerToken(),
                'apiVersion' => '2019-08-06',
            ]);
        } catch (\Exception $e) {
            $this->initialized = null;
            throw new \Exception('Payplug API can\'t be initialized. Error thrown: ' . $e->getMessage());
        }
    }
}