<?php

/**
 * Part of the Trillium
 *
 * @author  Kilte Leichnam <nwotnbm@gmail.com>
 * @package Trillium
 */

namespace Trillium\Provider;

use Vermillion\Container;
use Vermillion\Provider\ServiceProviderInterface;
use Vermillion\Provider\SubscriberProviderInterface;

/**
 * ControllerResponse Class
 *
 * @package Trillium\Provider
 */
class ControllerResponse implements ServiceProviderInterface, SubscriberProviderInterface
{

    /**
     * {@inheritdoc}
     */
    public function registerServices(Container $container)
    {
        $container['controller.response.subscriber'] = function ($container) {
            /** @var $conf \Vermillion\Configuration\Configuration */
            $conf   = $container['configuration'];
            $config = $conf->load('controller')->get();

            return new \Trillium\Subscriber\ControllerResponse($config, $container['view']);
        };
    }

    /**
     * {@inheritdoc}
     */
    public function getSubscribers(Container $container)
    {
        return [$container['controller.response.subscriber']];
    }

}
