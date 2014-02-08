<?php

/**
 * Part of the Trillium
 *
 * @author  Kilte Leichname <nwotnbm@gmail.com>
 * @package Trillium
 */

namespace Trillium\General\Configuration;

use Symfony\Component\Config\Loader\LoaderResolver;

/**
 * Configuration Class
 *
 * @method Configuration get(\string $key = null, \string $default = null)
 * @method Configuration has(\string $key)
 *
 * @package Trillium\General\Configuration
 */
class Configuration
{

    /**
     * Path to the configuration directory
     */
    const DIRECTORY = '/../../../../resources/configuration/';

    /**
     * @var array Resources collection
     */
    private $resourceCollection;

    /**
     * @var array Paths to the configuration files
     */
    private $paths;

    /**
     * @var string Environment
     */
    private $environment;

    /**
     * @var LoaderResolver Resolver
     */
    private $resolver;

    /**
     * @var Resource Default resource
     */
    private $defaultResource;

    /**
     * Constructor
     *
     * @param string         $environment Application environment
     * @param LoaderResolver $resolver    Resolver
     *
     * @return self
     */
    public function __construct($environment, LoaderResolver $resolver)
    {
        $this->resourceCollection = [];
        $this->environment        = $environment;
        $this->resolver           = $resolver;
        $this->paths              = [
            __DIR__ . self::DIRECTORY . $this->environment . '/',
            __DIR__ . self::DIRECTORY . 'default/',
        ];
    }

    /**
     * Enables access to the default resource methods
     *
     * @param string $name Method name
     * @param array  $args Arguments
     *
     * @throws \BadMethodCallException
     * @return mixed
     */
    public function __call($name, array $args = [])
    {
        if (!method_exists($this->defaultResource, $name)) {
            throw new \BadMethodCallException(sprintf('Method "%s" does not exists', $name));
        }

        return call_user_func_array([$this->defaultResource, $name], $args);
    }

    /**
     * Returns a resolver instance
     *
     * @return LoaderResolver
     */
    public function getResolver()
    {
        return $this->resolver;
    }

    /**
     * Returns list of paths to the configuration files
     * You can to use it as paths for a file locator
     *
     * @return array
     */
    public function getPaths()
    {
        return $this->paths;
    }

    /**
     * Loads and sets resource as default
     *
     * @param string      $resource Resource
     * @param string|null $type     Resource type
     *
     * @return void
     */
    public function setDefault($resource, $type = null)
    {
        $this->defaultResource = $this->load($resource, $type);
    }

    /**
     * Loads a resource
     *
     * @param string      $resource Resource
     * @param string|null $type     Resource type
     *
     * @throws \LogicException
     * @return Resource
     */
    public function load($resource, $type = null)
    {
        if (!array_key_exists($resource, $this->resourceCollection)) {
            $loader = $this->resolver->resolve($resource, $type);
            if ($loader === false) {
                throw new \LogicException('Can not to find a loader for the given resource');
            }
            $this->resourceCollection[$resource] = new Resource($loader->load($resource, $type));
        }

        return $this->resourceCollection[$resource];
    }

}
