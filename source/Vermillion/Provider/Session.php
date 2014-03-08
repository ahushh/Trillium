<?php

/**
 * Part of the Vermillion
 *
 * @author  Kilte Leichnam <nwotnbm@gmail.com>
 * @package Vermillion
 */

namespace Vermillion\Provider;

use Symfony\Component\HttpFoundation\Session\Storage\Handler\NativeFileSessionHandler;
use Symfony\Component\HttpFoundation\Session\Storage\NativeSessionStorage;
use Vermillion\Container;
use Vermillion\Session\Subscriber;

/**
 * Session Class
 *
 * @package Vermillion\Provider
 */
class Session implements ServiceProviderInterface, SubscriberProviderInterface
{

    /**
     * {@inheritdoc}
     */
    public function registerServices(Container $container)
    {
        $container['session.options']    = [];
        $container['session.save_path']  = null;
        $container['session']            = function ($container) {
            return new \Symfony\Component\HttpFoundation\Session\Session(
                new NativeSessionStorage(
                    $container['session.options'],
                    new NativeFileSessionHandler($container['session.save_path'])
                )
            );
        };
        $container['session.subscriber'] = function ($container) {
            return new Subscriber($container['session']);
        };
    }

    /**
     * {@inheritdoc}
     */
    public function getSubscribers(Container $container)
    {
        return [$container['session.subscriber']];
    }

}
