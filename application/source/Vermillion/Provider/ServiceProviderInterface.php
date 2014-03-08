<?php

/**
 * Part of the Vermillion
 *
 * @author  Kilte Leichnam <nwotnbm@gmail.com>
 * @package Vermillion
 */

namespace Vermillion\Provider;

use Vermillion\Container;

/**
 * ServiceProviderInterface Interface
 *
 * @package Vermillion\Provider
 */
interface ServiceProviderInterface
{

    /**
     * Registers services
     *
     * @param Container $container A container instance
     *
     * @return void
     */
    public function registerServices(Container $container);

}
