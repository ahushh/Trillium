<?php

/**
 * Part of the Trillium
 *
 * @author  Kilte Leichnam <nwotnbm@gmail.com>
 * @package Trillium
 */

namespace Trillium\General;

/**
 * Controller Class
 *
 * Base controller class
 *
 * @package Trillium\General
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
