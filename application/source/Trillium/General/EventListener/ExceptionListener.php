<?php

/**
 * Part of the Trillium
 *
 * @author  Kilte Leichnam <nwotnbm@gmail.com>
 * @package Trillium
 */

namespace Trillium\General\EventListener;

use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Trillium\General\Application;
use Trillium\General\ExceptionController;

/**
 * ExceptionListener Class
 *
 * @package Trillium\General\EventListener
 */
class ExceptionListener implements EventSubscriberInterface
{

    /**
     * @var ExceptionController A controller instance
     */
    private $controller;

    /**
     * @var Application An application instance
     */
    private $app;

    /**
     * @var LoggerInterface|null Logger instance
     */
    private $logger;

    /**
     * Constructor
     *
     * @param ExceptionController $controller Controller
     * @param Application         $app        An application instance
     * @param LoggerInterface     $logger     A logger instance
     *
     * @return self
     */
    public function __construct(ExceptionController $controller, Application $app, LoggerInterface $logger = null)
    {
        $this->controller = $controller;
        $this->app        = $app;
        $this->logger     = $logger;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::EXCEPTION => ['onKernelException', -128],
        ];
    }

    /**
     * Runs exception controller
     *
     * @param GetResponseForExceptionEvent $event
     *
     * @throws \LogicException
     * @return void
     */
    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        $exception = $event->getException();
        $code = $exception instanceof HttpExceptionInterface ? $exception->getStatusCode() : 500;
        $this->logException($exception);
        $response = call_user_func($this->controller, $this->app, $exception, $code);
        if (!$response instanceof Response) {
            throw new \LogicException('Exception controller must return a response instance');
        }
        $event->setResponse($response);
    }

    /**
     * Log exception
     *
     * @param \Exception $exception Exception instance
     *
     * @return void
     */
    private function logException(\Exception $exception)
    {
        if (!$this->logger !== null) {
            $this->logger->error(
                sprintf(
                    'Uncaught PHP Exception %s: "%s" at %s line %s',
                    get_class($exception),
                    $exception->getMessage(),
                    $exception->getFile(),
                    $exception->getLine()
                ),
                ['exception' => $exception]
            );
        }
    }

}
