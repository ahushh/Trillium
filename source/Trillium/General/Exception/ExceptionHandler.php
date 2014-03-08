<?php

/**
 * Part of the Trillium
 *
 * @author  Kilte Leichnam <nwotnbm@gmail.com>
 * @package Trillium
 */

namespace Trillium\General\Exception;

use Symfony\Component\Debug\Exception\FlattenException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * ExceptionHandler Class
 *
 * @package Trillium\General\Exception
 */
class ExceptionHandler
{

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
         * @var $exception FlattenException
         */
        $exception = $request->attributes->get('exception');

        return new Response($exception->getMessage(), $exception->getStatusCode());
    }

}
