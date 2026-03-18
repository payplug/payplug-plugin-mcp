<?php

declare(strict_types=1);

namespace PayplugPluginCore\services;

use Payplug\Payplug;

class Api
{
    private string $bearer_token;

    private const string PAYMENT_CREATE = '\Payplug\Payment::create';

    /**
     * @param array<string, mixed> $datas
     * @return array<string, mixed>
     */
    public function createPaymentResource(array $datas): array
    {
        return $this->doRequest(self::PAYMENT_CREATE, $datas);
    }

    public function getBearerToken(): string
    {
        return $this->bearer_token;
    }

    private function setBearerToken(string $bearer_token): void
    {
        $this->bearer_token = $bearer_token;
    }

    /**
     * @return $this
     * @throws \Payplug\Exception\ConfigurationException
     */
    public function load(string $bearer_token): self
    {
        $this->setBearerToken($bearer_token);
        $this->initialize();

        return $this;
    }

    /**
     * @param array<string, mixed> $params
     * @return array<string, mixed>
     */
    protected function doRequest(string $callback, array $params): array
    {
        try {
            $response = [
                'result'   => true,
                'response' => \call_user_func_array($callback, $params),
                'code'     => 200,
            ];
        } catch (\Exception $e) {
            $response = [
                'result'   => false,
                'response' => null,
                'code'     => $e->getCode(),
            ];
        }

        return $response;
    }

    /**
     * @throws \Exception
     */
    private function initialize(): void
    {
        try {
            Payplug::init([
                'secretKey'  => $this->getBearerToken(),
                'apiVersion' => '2019-08-06',
            ]);
        } catch (\Exception $e) {
            throw new \Exception('Payplug API can\'t be initialized. Error thrown: ' . $e->getMessage());
        }
    }
}
