<?php

/**
 * Part of the Trillium
 *
 * @package Trillium
 */

namespace Trillium\Controller;

use Trillium\Silex\Application;

/**
 * Controller Class
 *
 * Base controller class
 *
 * @package Trillium\Controller
 */
abstract class Controller
{
    /**
     * @var Application Silex application instance
     */
    protected $app;

    /**
     * Create Controller instance
     *
     * @param Application $app Silex application instance
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
    }

}
