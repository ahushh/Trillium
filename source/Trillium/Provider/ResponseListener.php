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
 * ResponseListener Class
 *
 * @package Trillium\Provider
 */
class ResponseListener implements ServiceProviderInterface, SubscriberProviderInterface
{

    /**
     * {@inheritdoc}
     */
    public function registerServices(Container $container)
    {
        $container['controller.response_listener']  = function ($c) {
            /** @var $conf \Vermillion\Configuration\Configuration */
            $conf   = $c['configuration'];
            $config = $conf->load('controller')->get();

            return new \Trillium\Controller\ResponseListener($config, $c['view']);
        };
        $container['http_kernel.response_listener'] = function ($c) {
            /** @var $conf \Vermillion\Configuration\Configuration */
            $conf = $c['configuration'];

            return new \Symfony\Component\HttpKernel\EventListener\ResponseListener($conf->get('charset'));
        };
    }

    /**
     * {@inheritdoc}
     */
    public function getSubscribers(Container $container)
    {
        return [
            $container['controller.response_listener'],
            $container['http_kernel.response_listener']
        ];
    }

}
