<?php

/**
 * Part of the Trillium
 *
 * @author  Kilte Leichnam <nwotnbm@gmail.com>
 * @package Trillium
 */

namespace Trillium\Service\Settings;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * RequestListener Class
 * Get user settings from a request
 *
 * @package Trillium\Service\Settings
 */
class RequestListener implements EventSubscriberInterface
{

    /**
     * @var Settings Settings
     */
    private $settings;

    /**
     * @var array Available keys
     */
    private $settingsKeys;

    /**
     * Constructor
     *
     * @param Settings $settings Settings
     *
     * @return self
     */
    public function __construct(Settings $settings)
    {
        $this->settings     = $settings;
        $this->settingsKeys = array_keys($settings->get(null, Settings::SYSTEM));
    }

    /**
     * Get user settings from a request
     *
     * @param GetResponseEvent $event An event instance
     *
     * @return void
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        if (!$event->isMasterRequest()) {
            return ;
        }
        $request      = $event->getRequest();
        $userSettings = [];
        foreach ($this->settingsKeys as $key) {
            $option = $request->cookies->get($key, null);
            if ($option !== null) {
                $userSettings[$key] = $option;
            }
        }
        $this->settings->set($userSettings, null, Settings::USER);
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::REQUEST => 'onKernelRequest',
        ];
    }

}