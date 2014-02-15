<?php

/**
 * Part of the Trillium
 *
 * @author  Kilte Leichnam <nwotnbm@gmail.com>
 * @package Trillium
 */

namespace Trillium\Service\Session;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * SessionSubscriber Class
 *
 * @package Trillium\Service\Session
 */
class SessionSubscriber implements EventSubscriberInterface
{

    /**
     * @var Session Session
     */
    private $session;

    /**
     * Constructor
     *
     * @param Session $session Session
     *
     * @return self
     */
    public function __construct(Session $session)
    {
        $this->session = $session;
    }

    /**
     * Sets session to a request
     *
     * @param GetResponseEvent $event Event
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
            KernelEvents::REQUEST  => ['onEarlyKernelRequest', 128],
        ];
    }
}
