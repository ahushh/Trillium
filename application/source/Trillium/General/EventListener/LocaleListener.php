<?php

/**
 * Part of the Trillium
 *
 * @author  Kilte Leichnam <nwotnbm@gmail.com>
 * @package Trillium
 */

namespace Trillium\General\EventListener;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use \Symfony\Component\HttpKernel\EventListener\LocaleListener as SymfonyLocaleListener;
use Symfony\Component\Routing\RequestContextAwareInterface;
use Trillium\General\Application;

/**
 * LocaleListener Class
 *
 * @package Trillium\General\EventListener
 */
class LocaleListener extends SymfonyLocaleListener
{

    /**
     * Path to the locale cookie
     */
    const COOKIE_PATH = 'trillium_locale';

    /**
     * @var Application An application instance
     */
    private $app;

    /**
     * Constructor
     *
     * @param Application                  $app          An application instance
     * @param RequestStack                 $requestStack A request stack instance
     * @param RequestContextAwareInterface $router       A request context aware interface
     *
     * @return self
     */
    public function __construct(Application $app, RequestStack $requestStack, RequestContextAwareInterface $router = null)
    {
        $this->app = $app;
        parent::__construct($app->getLocale(), $router, $requestStack);
    }

    /**
     * Sets locale to the application
     *
     * @param GetResponseEvent $event
     *
     * @return void
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        parent::onKernelRequest($event);
        $request = $event->getRequest();
        $this->app->setLocale($request->cookies->get(self::COOKIE_PATH, $request->getLocale()));
    }

}
