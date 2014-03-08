<?php

/**
 * Part of the Vermillion
 *
 * @author  Kilte Leichnam <nwotnbm@gmail.com>
 * @package Vermillion
 */

namespace Vermillion\Configuration;

use Symfony\Component\Config\Loader\LoaderInterface;

/**
 * Configuration Class
 *
 * @method mixed   get(string $key = null, string $default = null)
 * @method boolean has(string $key)
 *
 * @package Vermillion\Configuration
 */
class Configuration
{

    /**
     * @var LoaderInterface
     */
    private $loader;

    /**
     * @var \Vermillion\Configuration\Resource[]
     */
    private $resourceCollection = [];

    /**
     * @var \Vermillion\Configuration\Resource|null
     */
    private $defaultResource = null;

    /**
     * Constructor
     *
     * @param LoaderInterface $loader A loader
     *
     * @return self
     */
    public function __construct(LoaderInterface $loader)
    {
        $this->loader = $loader;
    }

    /**
     * Enables access to the default resource methods
     *
     * @param string $name Method name
     * @param array  $args Arguments
     *
     * @throws \RuntimeException
     * @throws \BadMethodCallException
     * @return mixed
     */
    public function __call($name, array $args = [])
    {
        if (!is_object($this->defaultResource)) {
            throw new \RuntimeException('Default resource is not defined');
        }
        if (!method_exists($this->defaultResource, $name)) {
            throw new \BadMethodCallException(sprintf('Method "%s" does not exists', $name));
        }

        return call_user_func_array([$this->defaultResource, $name], $args);
    }

    /**
     * Sets a resource as default
     *
     * @param \Vermillion\Configuration\Resource|string $resource The resource
     * @param string|null                               $type     The resource type
     *
     * @return void
     */
    public function setDefault($resource, $type = null)
    {
        if ($resource instanceof Resource) {
            $this->defaultResource = $resource;
        } else {
            $this->defaultResource = $this->load($resource, $type);
        }

    }

    /**
     * Loads a resource
     *
     * @param string      $resource The resource
     * @param string|null $type     The resource type
     *
     * @throws \RuntimeException
     * @return \Vermillion\Configuration\Resource
     */
    public function load($resource, $type = null)
    {
        if (!array_key_exists($resource, $this->resourceCollection)) {
            $this->resourceCollection[$resource] = new Resource($this->loader->load($resource, $type));
        }

        return $this->resourceCollection[$resource];
    }

}
