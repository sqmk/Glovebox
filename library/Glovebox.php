<?php
/**
 * Glovebox: Lightweight Dependency Injection Container
 *
 * @author    Michael Squires <sqmk@php.net>
 * @copyright Copyright (c) 2012 Michael K. Squires
 * @license   http://github.com/sqmk/LICENSE.txt
 * @package   Glovebox
 */

/**
 * Glovebox Container
 *
 * @package Glovebox
 */
class Glovebox implements \ArrayAccess
{
    /**
     * Services
     *
     * @var array
     */
    protected $services = [];

    /**
     * Parameters
     *
     * @var array
     */
    protected $parameters = [];

    /**
     * Get list of services
     *
     * @return array List of services
     */
    public function getServices()
    {
        return array_keys($this->services);
    }

    /**
     * Get list of parameters
     *
     * @return array List of parameters
     */
    public function getParameters()
    {
        return array_keys($this->parameters);
    }

    /**
     * Get service
     *
     * @param string $serviceName Service name
     *
     * @return mixed Generated service
     */
    public function __get($serviceName)
    {
        // Fail if service isn't known
        if (!isset($this->services[$serviceName])) {
            throw new \DomainException("Unknown service: {$serviceName}");
        }

        $service = $this->services[$serviceName];

        // Return persistent service immediately
        if (!($service->value instanceof \Closure)) {
            return $service->value;
        }

        return $service->persist === true
             ? $service->value = call_user_func_array($service->value, [$this])
             : call_user_func_array($service->value, [$this]);
    }

    /**
     * Set a service
     *
     * @param string $serviceName Service name
     * @param \Closure $factory Service generator
     *
     * @return void
     */
    public function __set($serviceName, \Closure $factory)
    {
        $this->services[$serviceName] = (object) [
            'value'   => $factory,
            'persist' => false
        ];
    }

    /**
     * Check if service exists
     *
     * @param string $serviceName Service name
     *
     * @return bool True if exists, false if not
     */
    public function __isset($serviceName)
    {
        return array_key_exists($serviceName, $this->services);
    }

    /**
     * Remove a service
     *
     * @param string $serviceName Service name
     *
     * @return void
     */
    public function __unset($serviceName)
    {
        unset($this->service[$serviceName]);
    }

    /**
     * Invoke for pulling service options
     *
     * @param string $serviceName Service name
     *
     * @return stdClass
     */
    public function __invoke($serviceName)
    {
        // Fail if service isn't known
        if (!isset($this->services[$serviceName])) {
            throw new \DomainException("Unknown service: {$serviceName}");
        }

        return $this->services[$serviceName];
    }

    /**
     * Check if parameter exists
     *
     * @param string $parameter Parameter name
     *
     * @return bool True if exists, false if not
     */
    public function offsetExists($parameter)
    {
        return array_key_exists($parameter, $this->parameters);
    }

    /**
     * Get parameter
     *
     * @param string $parameter Parameter name
     *
     * @return mixed Parameter value
     */
    public function offsetGet($parameter)
    {
        if (!$this->offsetExists($parameter)) {
            throw new \DomainException("Unknown parameter: {$parameter}");
        }

        return $this->parameters[$parameter];
    }

    /**
     * Set parameter
     *
     * @param string $parameter Parameter name
     * @param mixed $value Value
     *
     * @return void
     */
    public function offsetSet($parameter, $value)
    {
        $this->parameters[$parameter] = $value;
    }

    /**
     * Unset parameter
     *
     * @param string $parameter Parameter name
     *
     * @return void
     */
    public function offsetUnset($parameter)
    {
        unset($this->parameters[$parameter]);
    }
}
