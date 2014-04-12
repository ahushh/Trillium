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
class ExceptionHandler
{

    /**
     * @var boolean Is debug?
     */
    private $debug;

    /**
     * Constructor
     *
     * @param boolean $debug Is debug?
     *
     * @return self
     */
    public function __construct($debug = false)
    {
        $this->debug = $debug;
    }

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
        if ($this->debug) {
            $error = [
                'error' => $exception->getMessage(),
                'trace' => $exception->getTrace()
            ];
        } else {
            $error = [
                'error' => $exception->getStatusCode() === 500 ? 'Internal Server Error' : $exception->getMessage()
            ];
        }

        return new JsonResponse($error, $exception->getStatusCode());
    }

}
