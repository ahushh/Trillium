<?php

/**
 * Part of the Trillium
 *
 * @author  Kilte Leichnam <nwotnbm@gmail.com>
 * @package Trillium
 */

namespace Trillium\Service\Configuration;

use Symfony\Component\Config\Loader\LoaderResolver;

/**
 * Configuration Class
 *
 * @method mixed get(\string $key = null, \string $default = null)
 * @method mixed has(\string $key)
 *
 * @package Trillium\Service\Configuration
 */
class Configuration
{

    /**
     * @var Resource[] Resources collection
     */
    private $resourceCollection;

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
     * @param LoaderResolver $resolver Resolver
     *
     * @return self
     */
    public function __construct(LoaderResolver $resolver)
    {
        $this->resourceCollection = [];
        $this->resolver           = $resolver;
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
     * @return \Trillium\Service\Configuration\Resource
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
