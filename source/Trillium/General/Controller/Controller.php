<?php

/**
 * Part of the Trillium
 *
 * @author  Kilte Leichnam <nwotnbm@gmail.com>
 * @package Trillium
 */

namespace Trillium\General\Controller;

use Trillium\General\Application;

/**
 * Controller Class
 *
 * Base controller class
 *
 * @package Trillium\General\Controller
 */
abstract class Controller
{

    /**
     * @var Application An application instance
     */
    protected $app;

    /**
     * Constructor
     *
     * @param Application $app An application instance
     *
     * @return self
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
    }

}
