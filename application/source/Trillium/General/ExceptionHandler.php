<?php

/**
 * Part of the Trillium
 *
 * @author  Kilte Leichnam <nwotnbm@gmail.com>
 * @package Trillium
 */

namespace Trillium\General;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * ExceptionHandler Class
 *
 * @package Trillium\General
 */
class ExceptionHandler
{

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
        $this->app = $app;
    }

    /**
     * Handles an exception
     *
     * @param Request $request
     *
     * @return Response
     */
    public function __invoke(Request $request)
    {
        /**
         * @var $exception \Exception
         */
        $exception = $request->attributes->get('exception');
        return new Response($exception->getMessage());
    }

}
