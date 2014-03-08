<?php

/**
 * Part of the Trillium
 *
 * @author  Kilte Leichnam <nwotnbm@gmail.com>
 * @package Trillium
 */

namespace Trillium\Provider;

use Symfony\Component\HttpKernel\EventListener\ResponseListener;
use Trillium\Subscriber\ControllerResponse;
use Vermillion\Container;
use Vermillion\Provider\ServiceProviderInterface;
use Vermillion\Provider\SubscriberProviderInterface;

/**
 * Subscriber Class
 *
 * @package Trillium\Provider
 */
class Subscriber implements ServiceProviderInterface, SubscriberProviderInterface
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

            return new ControllerResponse($config, $container['view']);
        };
        $container['response.subscriber']            = function ($container) {
            /** @var $conf \Vermillion\Configuration\Configuration */
            $conf = $container['configuration'];

            return new ResponseListener($conf->get('charset'));
        };
    }

    /**
     * {@inheritdoc}
     */
    public function getSubscribers(Container $container)
    {
        return [
            $container['controller.response.subscriber'],
            $container['response.subscriber']
        ];
    }

}
