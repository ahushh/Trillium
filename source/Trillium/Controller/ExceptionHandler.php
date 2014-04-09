<?php

/**
 * Part of the Trillium
 *
 * @author  Kilte Leichnam <nwotnbm@gmail.com>
 * @package Trillium
 */

namespace Trillium\Controller;

use Symfony\Component\Debug\Exception\FlattenException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * ExceptionHandler Class
 *
 * @package Trillium\Controller
 */
class ExceptionHandler extends Controller
{

    /**
     * Handles an exception
     *
     * @param Request $request
     *
     * @throws \InvalidArgumentException
     * @return JsonResponse
     */
    public function __invoke(Request $request)
    {
        /**
         * @var $exception FlattenException
         */
        $exception = $request->attributes->get('exception');

        return new JsonResponse($exception->getMessage(), $exception->getStatusCode());
    }

}
