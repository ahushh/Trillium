<?php

/**
 * Part of the Trillium
 *
 * @author  Kilte Leichnam <nwotnbm@gmail.com>
 * @package Trillium
 */

namespace Trillium\Service\Translator;

use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\EventListener\LocaleListener;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Routing\RequestContextAwareInterface;
use Symfony\Component\Translation\Translator;

/**
 * Subscriber Class
 *
 * @package Trillium\Service\Translator
 */
class Subscriber extends LocaleListener
{

    /**
     * @var string Path to the locales directory
     */
    private $localesDirectory;

    /**
     * @var Translator Translator instance
     */
    private $translator;

    /**
     * @var string Path to the locale cookie
     */
    private $cookieName;

    /**
     * @var int The time the cookie expires
     */
    private $cookieExpire;

    /**
     * @var string The name of the loader
     * @see \Symfony\Component\Translation\Translator::addLoader()
     */
    private $resourceType;

    /**
     * Constructor
     *
     * @param Translator                   $translator       Translator instance
     * @param string                       $localesDirectory Directory with locales
     * @param string                       $resourceType     The name of the loader (@see Translator::addLoader())
     * @param string                       $defaultLocale    An application instance
     * @param RequestStack                 $requestStack     A request stack instance
     * @param RequestContextAwareInterface $router           A request context aware interface
     * @param string                       $cookieName       Path to the locale cookie
     * @param int                          $cookieExpire     The time the cookie expires (1 year by default)
     *
     * @see \Symfony\Component\Translation\Translator::addLoader()
     * @throws \InvalidArgumentException
     * @return self
     */
    public function __construct(
        Translator $translator,
        $localesDirectory,
        $resourceType,
        $defaultLocale,
        RequestStack $requestStack,
        RequestContextAwareInterface $router = null,
        $cookieName = 'vermillion_locale',
        $cookieExpire = 31536000
    ) {
        if (!is_dir($localesDirectory)) {
            throw new \InvalidArgumentException(sprintf('Locales directory "%s" does not exists', $localesDirectory));
        }
        $this->translator       = $translator;
        $this->localesDirectory = $localesDirectory;
        $this->cookieName       = $cookieName;
        $this->cookieExpire     = $cookieExpire;
        $this->resourceType     = $resourceType;
        parent::__construct($defaultLocale, $router, $requestStack);
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
        $locale  = $request->cookies->get($this->cookieName, null);
        if ($locale === null) {
            $languages = $request->getLanguages();
            foreach ($languages as $l) {
                $resource = $this->localesDirectory . $l . '.' . $this->resourceType;
                if (is_file($resource)) {
                    $locale = $l;
                    break;
                }
            }
            if ($locale === null) {
                $resource = null;
            }
        } else {
            $resource = $this->localesDirectory . $locale . '.' . $this->resourceType;
            if (!is_file($resource)) {
                $locale   = null;
                $resource = null;
            }
        }
        if (isset($resource) && $resource !== null) {
            $this->translator->setLocale($locale);
            $this->translator->addResource($this->resourceType, $resource, $locale);
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
        if ($request->cookies->get($this->cookieName, null) === null) {
            $response = $event->getResponse();
            $response->headers->setCookie(
                new Cookie($this->cookieName, $this->translator->getLocale(), time() + $this->cookieExpire)
            );
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
