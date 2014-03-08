<?php

/**
 * Part of the Trillium
 *
 * @author  Kilte Leichnam <nwotnbm@gmail.com>
 * @package Trillium
 */

namespace Trillium\Service\Security\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\HttpKernel;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\SecurityContext;
use Trillium\Service\Security\Provider\AdvancedUserProviderInterface;
use Trillium\Service\Security\User\AdvancedUserInterface;

/**
 * ActivityListener Class
 *
 * Update last activity of a user on each request
 *
 * @package Trillium\Service\Security\EventListener
 */
class ActivityListener implements EventSubscriberInterface
{

    /**
     * @var AdvancedUserProviderInterface User provider
     */
    private $userProvider;

    /**
     * @var SecurityContext Security context
     */
    private $security;

    /**
     * Create ActivityListener instance
     *
     * @param SecurityContext               $security     Security context
     * @param AdvancedUserProviderInterface $userProvider User provider
     *
     * @return ActivityListener
     */
    public function __construct(SecurityContext $security, AdvancedUserProviderInterface $userProvider)
    {
        $this->userProvider = $userProvider;
        $this->security     = $security;
    }

    /**
     * Update last activity of a user on each request
     *
     * @param GetResponseEvent $event
     *
     * @return void
     */
    public function onCoreController(GetResponseEvent $event)
    {
        if ($event->getRequestType() !== HttpKernel::MASTER_REQUEST) {
            return;
        }
        $token = $this->security->getToken();
        $user  = $token !== null ? $token->getUser() : null;
        if ($user instanceof AdvancedUserInterface) {
            if ($user->getLastActivity() < time() - AdvancedUserProviderInterface::LAST_ACTIVITY_DELAY) {
                $user->setLastActivity(time());
                $this->userProvider->update($user);
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::REQUEST => 'onCoreController',
        ];
    }

}
