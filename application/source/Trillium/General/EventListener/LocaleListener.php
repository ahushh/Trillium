<?php

/**
 * Part of the Trillium
 *
 * @author  Kilte Leichnam <nwotnbm@gmail.com>
 * @package Trillium
 */

namespace Trillium\General\EventListener;

use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use \Symfony\Component\HttpKernel\EventListener\LocaleListener as SymfonyLocaleListener;
use Symfony\Component\HttpKernel\KernelEvents;
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
    const COOKIE_NAME = 'trillium_locale';

    /**
     * The time the cookie expires (1 year)
     */
    const COOKIE_EXPIRE = 31536000;

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
        $locale = $request->cookies->get(self::COOKIE_NAME, null);
        if ($locale === null) {
            $languages = $request->getLanguages();
            foreach ($languages as $l) {
                $resource = $this->app->getDirectory('locales') . $l . '.json';
                if (is_file($resource)) {
                    $locale = $l;
                    break;
                }
            }
            if ($locale === null) {
                $resource = null;
            }
        } else {
            $resource = $this->app->getDirectory('locales') . $locale . '.json';
            if (!is_file($resource)) {
                $locale = null;
                $resource = null;
            }
        }
        if (isset($resource) && $resource !== null) {
            $this->app->setLocale($locale);
            $this->app->translator->setLocale($locale);
            $this->app->translator->addResource('json', $resource, $locale);
        }
    }

    /**
     * Sets cookie to a response
     *
     * @param FilterResponseEvent $event
     *
     * @return void
     */
    public function onKernelResponse(FilterResponseEvent $event)
    {
        $request = $event->getRequest();
        if ($request->cookies->get(self::COOKIE_NAME, null) === null) {
            $response = $event->getResponse();
            $response->headers->setCookie(new Cookie(self::COOKIE_NAME, $this->app->getLocale(), time() + self::COOKIE_EXPIRE));
        }
    }

    /**
     * Returns the list of the subscribed events
     *
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return array_merge(
            parent::getSubscribedEvents(),
            [
                KernelEvents::RESPONSE => 'onKernelResponse',
            ]
        );
    }

}
