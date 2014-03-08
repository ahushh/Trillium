<?php

/**
 * Part of the Trillium
 *
 * @author  Kilte Leichnam <nwotnbm@gmail.com>
 * @package Trillium
 */

namespace Trillium\Provider;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Vermillion\Container;
use Vermillion\Provider\ServiceProviderInterface;
use Vermillion\Provider\SubscriberProviderInterface;

/**
 * ResponseListener Class
 *
 * @package Trillium\Provider
 */
class ResponseListener implements ServiceProviderInterface, SubscriberProviderInterface
{

    /**
     * Registers services
     *
     * @param Container $container A container instance
     *
     * @return void
     */
    public function registerServices(Container $container)
    {
        $container['response.subscriber'] = function ($container) {
            /** @var $conf \Vermillion\Configuration\Configuration */
            $conf = $container['configuration'];

            return new \Symfony\Component\HttpKernel\EventListener\ResponseListener($conf->get('charset'));
        };
    }

    /**
     * Returns event subscribers
     *
     * @param Container $container A container instance
     *
     * @return EventSubscriberInterface[]
     */
    public function getSubscribers(Container $container)
    {
        return [$container['response.subscriber']];
    }

}
