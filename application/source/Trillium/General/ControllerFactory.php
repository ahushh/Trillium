<?php

/**
 * Part of the Trillium
 *
 * @author  Kilte Leichnam <nwotnbm@gmail.com>
 * @package Trillium
 */

namespace Trillium\General;

/**
 * ControllerFactory Class
 *
 * @package Trillium\General
 */
class ControllerFactory
{

    /**
     * @var array Controllers
     */
    private $controllers;

    /**
     * @var Application An application instance
     */
    private $app;

    /**
     * Constructor
     *
     * @param Application $app An application instance
     *
     * @return self
     */
    public function __construct(Application $app)
    {
        $this->controllers = [];
        $this->app = $app;
    }

    /**
     * Creates a controller instance
     *
     * @param string $controller Full class name
     *
     * @throws \InvalidArgumentException
     * @return mixed
     */
    private function create($controller)
    {
        if (!class_exists($controller)) {
            throw new \InvalidArgumentException(sprintf('Controller class "%s" does not exists', $controller));
        }
        $instance = new $controller($this->app);
        if (!$instance instanceof Controller) {
            throw new \InvalidArgumentException(sprintf(
                'Controller "%s" must be instance of \Trillium\General\Controller',
                $controller
            ));
        }

        return $instance;
    }

    /**
     * Checks, whether controller exists for a given route
     *
     * @param string $route A route name
     *
     * @return boolean
     */
    public function has($route)
    {
        return array_key_exists($route, $this->controllers);
    }

    /**
     * Returns a controller instance
     *
     * @param string $route      A route name
     * @param string $controller Full class name of controller
     *
     * @return mixed
     */
    public function get($route, $controller)
    {
        if (!$this->has($route)) {
            $this->controllers[$route] = $this->create($controller);
        }

        return $this->controllers[$route];
    }

}
