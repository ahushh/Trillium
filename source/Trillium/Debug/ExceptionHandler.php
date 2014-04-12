<?php

/**
 * Part of the Trillium
 *
 * @author  Kilte Leichnam <nwotnbm@gmail.com>
 * @package Trillium
 */

namespace Trillium\Debug;

use Symfony\Component\Debug\Exception\FlattenException;
use Symfony\Component\Debug\ExceptionHandler as BaseExceptionHandler;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * ExceptionHandler Class
 *
 * @package Trillium\Debug
 */
class ExceptionHandler extends BaseExceptionHandler
{

    /**
     * @var boolean Is Debug
     */
    private $isDebug;

    /**
     * Constructor
     *
     * @param boolean $debug Is Debug
     *
     * @return self
     */
    public function __construct($debug)
    {
        $this->isDebug = $debug;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(\Exception $exception)
    {
        if (!$exception instanceof FlattenException) {
            $exception = FlattenException::create($exception);
        }
        if ($this->isDebug) {
            $error = [
                'error' => $exception->getMessage(),
                'trace' => $exception->getTrace()
            ];
        } else {
            $error = [
                'error' => $exception->getStatusCode() === 500 ? 'Internal Server Error' : $exception->getMessage()
            ];
        }
        (new JsonResponse($error, $exception->getStatusCode()))->send();
    }

}
