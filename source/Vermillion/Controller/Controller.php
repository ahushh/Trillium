<?php

/**
 * Part of the Vermillion
 *
 * @author  Kilte Leichnam <nwotnbm@gmail.com>
 * @package Vermillion
 */

namespace Vermillion\Controller;

/**
 * Controller Class
 *
 * @package Vermillion\Controller
 */
abstract class Controller
{

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
        $this->container = $container;
    }

}
