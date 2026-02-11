<?php

namespace PayplugPluginCore\Utilities\Traits;

trait DependenciesLoader
{
    /** @var array */
    private $allowed_services = [
        'api',
    ];

    /** @var array */
    private $allowed_gateways = [
        'payment',
    ];

    /**
     * @param $name
     *
     * @throws \Exception
     *
     * @return mixed
     */
    public function get_service(string $name = ''): object
    {
        if (empty($name)) {
            throw new \Exception('Invalid parameter, $name given should be a non empty string.');
        }

        if (!in_array($name, $this->allowed_services)) {
            throw new \Exception('Given $name is not allower services.');
        }

        $service_name = '\PayplugPluginCore\Utilities\Services\\' . str_replace('_', '', ucwords($name, '_'));
        if (!class_exists($service_name)) {
            throw new \Exception('Service can\'t be found.');
        }

        return new $service_name();
    }

    /**
     * @param $name
     *
     * @throws \Exception
     *
     * @return mixed
     */
    public function get_gateway(string $name = ''): object
    {
        if (empty($name)) {
            throw new \Exception('Invalid parameter, $name given should be a non empty string.');
        }

        if (!in_array($name, $this->allowed_gateways)) {
            throw new \Exception('Given $name is not allower gateways.');
        }

        $gateway_name = '\PayplugPluginCore\gateways\\' . str_replace('_', '', ucwords($name, '_')) . 'Gateway';
        if (!class_exists($gateway_name)) {
            throw new \Exception('Gateway can\'t be found.');
        }

        return new $gateway_name();
    }
}
