<?php

/**
 * Part of the Vermillion
 *
 * @author  Kilte Leichnam <nwotnbm@gmail.com>
 * @package Vermillion
 */

namespace Vermillion\Controller;

use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ControllerResolver;

/**
 * Resolver Class
 *
 * @package Vermillion\Controller
 */
class Resolver extends ControllerResolver
{

    /**
     * @var Factory A ControllerFactory instance
     */
    private $factory;

    /**
     * Constructor
     *
     * @param Factory         $factory A ControllerFactory instance
     * @param LoggerInterface $logger  A LoggerInterface instance
     *
     * @return self
     */
    public function __construct(Factory $factory, LoggerInterface $logger = null)
    {
        $this->factory = $factory;
        parent::__construct($logger);
    }

    /**
     * {@inheritdoc}
     */
    public function getController(Request $request)
    {
        $route      = $request->attributes->get('_route');
        $controller = $request->attributes->get('_controller');
        $action     = $request->attributes->get('_action');
        if (is_callable($controller)) {
            return $controller;
        } elseif (!is_string($route) || !is_string($controller) || !is_string($action)) {
            return false;
        }

        return [$this->factory->get($route, $controller), $action];
    }

}
