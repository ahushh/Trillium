<?php

/**
 * Part of the Trillium
 *
 * @author  Kilte Leichnam <nwotnbm@gmail.com>
 * @package Trillium
 */

namespace Trillium\Service\Twig;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Twig_Environment;

/**
 * RequestListener Class
 *
 * @package Trillium\Service\Twig
 */
class RequestListener implements EventSubscriberInterface
{

    /**
     * @var Twig_Environment Twig
     */
    private $twig;

    /**
     * Constructor
     *
     * @param Twig_Environment $twig Twig
     *
     * @return self
     */
    public function __construct(Twig_Environment $twig)
    {
        $this->twig= $twig;
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
            $this->twig->addExtension(new CommonExtension($request->getSchemeAndHttpHost()));
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
