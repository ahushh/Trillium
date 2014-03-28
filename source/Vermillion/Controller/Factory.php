<?php

/**
 * Part of the Vermillion
 *
 * @author  Kilte Leichnam <nwotnbm@gmail.com>
 * @package Vermillion
 */

namespace Vermillion\Controller;

/**
 * Factory Class
 *
 * @package Vermillion\Controller
 */
class Factory
{

    /**
     * @var array Controllers
     */
    protected $controllers;

    /**
     * @var \Pimple A container instance
     */
    protected $container;

    /**
     * Constructor
     *
     * @param \Pimple $container A container instance
     *
     * @return self
     */
    public function __construct(\Pimple $container)
    {
        $this->controllers = [];
        $this->container   = $container;
    }

    /**
     * Creates a controller instance
     *
     * @param string $controller Full class name
     *
     * @throws \InvalidArgumentException
     * @return mixed
     */
    protected function create($controller)
    {
        if (!class_exists($controller)) {
            throw new \InvalidArgumentException(sprintf('Controller class "%s" does not exists', $controller));
        }
        $instance = new $controller($this->container);
        if (!$instance instanceof Controller) {
            throw new \InvalidArgumentException(
                sprintf(
                    'Controller "%s" must be instance of \Vermillion\Controller\Controller',
                    $controller
                )
            );
        }

        return $instance;
    }

    /**
     * {@inheritdoc}
     */
    public function get($route, $controller)
    {
        if (!array_key_exists($route, $this->controllers)) {
            $this->controllers[$route] = $this->create($controller);
        }

        return $this->controllers[$route];
    }

}
