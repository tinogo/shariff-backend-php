<?php

namespace Heise\Shariff\Backend;

use GuzzleHttp\ClientInterface;

/**
 * Class ServiceFactory.
 */
class ServiceFactory
{
    /**
     * @var array
     */
    protected static $availableServicesMap = [
        'addthis' => 'AddThis',
        'facebook' => 'Facebook',
        'flattr' => 'Flattr',
        'linkedin' => 'LinkedIn',
        'pinterest' => 'Pinterest',
        'reddit' => 'Reddit',
        'stumbleupon' => 'StumbleUpon',
        'vk' => 'Vk',
        'xing' => 'Xing',
    ];

    /** @var ClientInterface */
    protected $client;

    /** @var ServiceInterface[] */
    protected $serviceMap = [];

    /**
     * @param ClientInterface $client
     */
    public function __construct(ClientInterface $client)
    {
        $this->client = $client;
    }

    /**
     * @param string           $name
     * @param ServiceInterface $service
     */
    public function registerService($name, ServiceInterface $service)
    {
        $this->serviceMap[strtolower($name)] = $service;
    }

    /**
     * @param array $serviceNames
     * @param array $config
     *
     * @return array
     */
    public function getServicesByName(array $serviceNames, array $config)
    {
        $services = [];
        foreach ($serviceNames as $serviceName) {
            try {
                $service = $this->createService($serviceName, $config);
            } catch (\InvalidArgumentException $e) {
                continue;
            }
            $services[] = $service;
        }

        return $services;
    }

    /**
     * @param string $serviceName
     * @param array  $config
     *
     * @return ServiceInterface
     */
    protected function createService($serviceName, array $config)
    {
        $normalizedServiceName = strtolower($serviceName);
        if (isset($this->serviceMap[$normalizedServiceName])) {
            $service = $this->serviceMap[$normalizedServiceName];
        } else {
            if (false === \in_array($normalizedServiceName, self::$availableServicesMap)) {
                throw new \InvalidArgumentException('Invalid service name "' . $serviceName . '".');
            }

            $serviceClass = '\\Heise\\Shariff\\Backend\\' . self::$availableServicesMap[$normalizedServiceName];
            $service = new $serviceClass($this->client);
        }

        if (isset($config[$serviceName])) {
            $service->setConfig($config[$serviceName]);
        }

        return $service;
    }
}
