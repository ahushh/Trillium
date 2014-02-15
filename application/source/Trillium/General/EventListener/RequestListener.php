<?php

/**
 * Part of the Trillium
 *
 * @author  Kilte Leichnam <nwotnbm@gmail.com>
 * @package Trillium
 */

namespace Trillium\General\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Trillium\General\Application;
use Trillium\Service\Twig\CommonExtension;

/**
 * RequestListener Class
 *
 * @package Trillium\General\EventListener
 */
class RequestListener implements EventSubscriberInterface
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
     * Adds the TwigExtension to a twig environment
     *
     * @param GetResponseEvent $event
     *
     * @return void
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        $request = $event->getRequest();
        if ($event->getRequestType() === HttpKernelInterface::MASTER_REQUEST) {
            $this->app->view->getEnvironment()->addExtension(new CommonExtension($request->getSchemeAndHttpHost()));
        }
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::REQUEST => 'onKernelRequest'
        ];
    }

}
