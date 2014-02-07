<?php

/**
 * Part of the Trillium
 *
 * @author  Kilte Leichnam <nwotnbm@gmail.com>
 * @package Trillium
 */

namespace Trillium\General;

use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ControllerResolver as SymfonyControllerResolver;

/**
 * ControllerResolver Class
 *
 * @package Trillium\General
 */
class ControllerResolver extends SymfonyControllerResolver
{

    /**
     * @var ControllerFactory A ControllerFactory instance
     */
    private $controllerFactory;

    /**
     * Constructor
     *
     * @param LoggerInterface   $logger            A LoggerInterface instance
     * @param ControllerFactory $controllerFactory A ControllerFactory instance
     *
     * @return self
     */
    public function __construct(LoggerInterface $logger, ControllerFactory $controllerFactory)
    {
        parent::__construct($logger);
        $this->controllerFactory = $controllerFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function getController(Request $request)
    {
        $route = $request->attributes->get('_route');
        $controller = $request->attributes->get('_controller');
        $action = $request->attributes->get('_action');
        if (is_callable($controller)) {
            return $controller;
        } elseif (!is_string($route) || !is_string($controller) || !is_string($action)) {
            return false;
        }

        return [$this->controllerFactory->get($route, $controller), $action];
    }

}
