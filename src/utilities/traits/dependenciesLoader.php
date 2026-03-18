<?php

declare(strict_types=1);

namespace PayplugPluginCore\Traits;

trait dependenciesLoader
{
    /** @var array<int, string> */
    private array $allowed_services = [];

    /** @var array<int, string> */
    private array $allowed_gateways = [];

    /**
     * @return object
     * @throws \Exception
     */
    public function get_service(string $name = ''): object
    {
        if (empty($name)) {
            throw new \Exception('Invalid parameter, $name given should be a non empty string.');
        }

        if (!\in_array($name, $this->allowed_services)) {
            throw new \Exception('Given $name is not allower services.');
        }

        $service_name = '\PayplugPluginCore\services\\' . str_replace('_', '', ucwords($name, '_'));
        if (!class_exists($service_name)) {
            throw new \Exception('Service can\'t be found.');
        }

        return new $service_name();
    }

    /**
     * @return object
     * @throws \Exception
     */
    public function get_gateway(string $name = ''): object
    {
        if (empty($name)) {
            throw new \Exception('Invalid parameter, $name given should be a non empty string.');
        }

        if (!\in_array($name, $this->allowed_gateways)) {
            throw new \Exception('Given $name is not allower gateways.');
        }

        $gateway_name = '\PayplugPluginCore\gateways\\' . str_replace('_', '', ucwords($name, '_')) . 'Gateway';
        if (!class_exists($gateway_name)) {
            throw new \Exception('Gateway can\'t be found.');
        }

        return new $gateway_name();
    }
}
