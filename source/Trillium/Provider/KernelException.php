<?php

/**
 * Part of the Trillium
 *
 * @author  Kilte Leichnam <nwotnbm@gmail.com>
 * @package Trillium
 */

namespace Trillium\Provider;

use Symfony\Component\HttpKernel\EventListener\ExceptionListener;
use Trillium\General\Exception\ExceptionHandler;
use Vermillion\Container;
use Vermillion\Provider\ServiceProviderInterface;
use Vermillion\Provider\SubscriberProviderInterface;

/**
 * KernelException Class
 *
 * @package Trillium\Provider
 */
class KernelException implements ServiceProviderInterface, SubscriberProviderInterface
{

    /**
     * {@inheritdoc}
     */
    public function registerServices(Container $container)
    {
        $container['kernel.exception.handler']    = function () {
            return new ExceptionHandler();
        };
        $container['kernel.exception.subscriber'] = function ($container) {
            return new ExceptionListener($container['kernel.exception.handler'], $container['logger']);
        };
    }

    /**
     * {@inheritdoc}
     */
    public function getSubscribers(Container $container)
    {
        return [$container['kernel.exception.subscriber']];
    }

}
