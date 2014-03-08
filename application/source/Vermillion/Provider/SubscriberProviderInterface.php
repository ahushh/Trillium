<?php

/**
 * Part of the Vermillion
 *
 * @author  Kilte Leichnam <nwotnbm@gmail.com>
 * @package Vermillion
 */

namespace Vermillion\Provider;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Vermillion\Container;

/**
 * SubscriberProviderInterface Interface
 *
 * @package Vermillion\Provider
 */
interface SubscriberProviderInterface
{

    /**
     * Returns event subscribers
     *
     * @param Container $container A container instance
     *
     * @return EventSubscriberInterface[]
     */
    public function getSubscribers(Container $container);

}
