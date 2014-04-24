<?php

/**
 * Part of the Vermillion
 *
 * @author  Kilte Leichnam <nwotnbm@gmail.com>
 * @package Vermillion
 */

namespace Vermillion\Provider;

use Monolog\Handler\StreamHandler;
use Monolog\Logger as Monolog;
use Vermillion\Container;

/**
 * Logger Class
 *
 * @package Vermillion\Provider
 */
class Logger implements ServiceProviderInterface
{

    /**
     * {@inheritdoc}
     */
    public function registerServices(Container $container)
    {
        $container['logger']       = function ($container) {
            /** @var $env \Vermillion\Environment */
            $env = $container['environment'];

            return $container['logger.factory']('vermillion', $env->getEnvironment());
        };
        $container['logger.factory'] = $container->protect(
            function ($name, $filename) use ($container) {
                /** @var $env \Vermillion\Environment */
                $env    = $container['environment'];
                $logger = new Monolog($name);
                $logger->pushHandler(
                    new StreamHandler(
                        $env->getDirectory('logs') . $filename . '.log',
                        $env->isDebug() ? Monolog::DEBUG : Monolog::ERROR
                    )
                );

                return $logger;
            }
        );

    }

}
