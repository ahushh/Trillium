<?php

/**
 * Part of the Trillium
 *
 * @author  Kilte Leichnam <nwotnbm@gmail.com>
 * @package Trillium
 */

namespace Trillium\General\Exception;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Trillium\General\Application;

/**
 * ExceptionHandler Class
 *
 * @package Trillium\General\Exception
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
         * @var $exception \Exception|HttpExceptionInterface
         */
        $exception = $request->attributes->get('exception');
        if ($exception instanceof HttpExceptionInterface) {
            $message = $exception->getMessage();
            $code = $exception->getStatusCode();
        } else {
            $message = 'Internal Server Error';
            $code = 500;
        }

        return new Response($message, $code);
    }

}
