<?php

declare(strict_types=1);

namespace PayplugPluginCore\Utilities\Services;

use Payplug\Payment;
use Payplug\Payplug;

class Api
{
    private string $bearer_token;

    /** @var array{0: class-string<Payment>, 1: string} */
    private const array PAYMENT_CREATE = [Payment::class, 'create'];

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
    protected function doRequest(callable $callback, array $params): array
    {
        try {
            $response = [
                'result'   => 'OK',
                'response' => \call_user_func_array($callback, $params),
                'code'     => 200, // not putting true here because it waits an HTTP code e.g : 200 => OK
            ];
        } catch (\Exception $e) {
            $response = [
                'message' => $e->getMessage(),
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
