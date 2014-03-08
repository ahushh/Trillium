<?php

/**
 * Part of the Vermillion
 *
 * @author  Kilte Leichnam <nwotnbm@gmail.com>
 * @package Vermillion
 */

namespace Vermillion\Session;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * Subscriber Class
 *
 * @package Vermillion\Session
 */
class Subscriber implements EventSubscriberInterface
{

    /**
     * @var SessionInterface
     */
    private $session;

    /**
     * Constructor
     *
     * @param SessionInterface $session
     *
     * @return self
     */
    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }

    /**
     * Sets session to a request
     *
     * @param GetResponseEvent $event
     *
     * @return void
     */
    public function onEarlyKernelRequest(GetResponseEvent $event)
    {
        $event->getRequest()->setSession($this->session);
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::REQUEST => ['onEarlyKernelRequest', 128]
        ];
    }

}
