<?php

/**
 * Part of the Trillium
 *
 * @author  Kilte Leichnam <nwotnbm@gmail.com>
 * @package Trillium
 */

namespace Trillium\General;

use Symfony\Component\HttpFoundation\Response;

/**
 * ExceptionController Class
 *
 * @package Trillium\General
 */
class ExceptionController
{

    /**
     * Exception controller
     *
     * Transforms an exception to a response
     *
     * @param Application $app       An application instance
     * @param \Exception  $exception An exception instance
     * @param int         $code      A HTTP status code
     *
     * @return Response
     */
    public function __invoke(Application $app, \Exception $exception, $code)
    {
        return new Response($exception->getMessage(), $code);
    }

}
