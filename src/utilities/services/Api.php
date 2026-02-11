<?php

namespace PayplugPluginCore\Utilities\Services;

use Payplug\Payment;
use Payplug\Payplug;

class Api
{
    /** @var true */
    private $initialized;
    /** @var string */
    private $bearer_token;

    /**
     * @param array $datas
     *
     * @return array
     */
    public function createPaymentResource(array $datas): array
    {
        try {
            $response = [
                'code' => 200,
                'message' => 'OK',
                'response' => Payment::create($datas, $this->initialized),
                'result' => true,
            ];
        } catch (\Exception $e) {
            $response = [
                'code' => $e->getCode(),
                'message' => $e->getMessage(),
                'response' => null,
                'result' => false,
            ];
        }

        return $response;
    }

    /**
     * @return string
     */
    public function getBearerToken(): string
    {
        return (string) $this->bearer_token;
    }

    /**
     * @param string $bearer_token
     *
     * @throws \Payplug\Exception\ConfigurationException
     *
     * @return $this
     */
    public function load(string $bearer_token)
    {
        $this->setBearerToken((string) $bearer_token);
        $this->initialize();

        return $this;
    }

    /**
     * @param string $bearer_token
     *
     * @return void
     */
    private function setBearerToken(string $bearer_token): void
    {
        $this->bearer_token = $bearer_token;
    }

    /**
     * @throws \Payplug\Exception\ConfigurationException
     *
     * @return void
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
