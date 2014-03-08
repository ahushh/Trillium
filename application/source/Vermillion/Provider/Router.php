<?php

/**
 * Part of the Vermillion
 *
 * @author  Kilte Leichnam <nwotnbm@gmail.com>
 * @package Vermillion
 */

namespace Vermillion\Provider;

use Symfony\Component\Config\Loader\DelegatingLoader;
use Symfony\Component\Config\Loader\LoaderResolver;
use Symfony\Component\HttpKernel\EventListener\RouterListener;
use Vermillion\Container;
use Vermillion\Routing\Loader\JsonFileLoader;
use Vermillion\Routing\Loader\YamlFileLoader;

/**
 * Router Class
 *
 * @package Vermillion\Provider
 */
class Router implements ServiceProviderInterface, SubscriberProviderInterface
{

    /**
     * {@inheritdoc}
     */
    public function registerServices(Container $container)
    {
        $container['router'] = function ($container) {
            /**
             * @var $env  \Vermillion\Environment
             * @var $conf \Vermillion\Configuration\Configuration
             */
            $env    = $container['environment'];
            $conf   = $container['configuration'];
            $router = new \Symfony\Component\Routing\Router(
                new DelegatingLoader(
                    new LoaderResolver([
                        new JsonFileLoader($container['configuration.locator']),
                        new YamlFileLoader($container['configuration.locator']),
                    ])
                ),
                'routes',
                [
                    'cache_dir'             => $env->getDirectory('cache'),
                    'debug'                 => $env->isDebug(),
                    'generator_cache_class' => 'CachedUrlGenerator',
                    'matcher_cache_class'   => 'CachedUrlMatcher',
                ],
                null,
                $container['logger']
            );
            $router->getContext()->setHttpPort($conf->get('http_port'));
            $router->getContext()->setHttpsPort($conf->get('https_port'));

            return $router;
        };
        $container['router.subscriber'] = function ($container) {
            /** @var $router \Symfony\Component\Routing\Router */
            $router = $container['router'];

            return new RouterListener(
                $router->getMatcher(),
                $router->getContext(),
                $container['logger'],
                $container['requestStack']
            );
        };
    }

    /**
     * {@inheritdoc}
     */
    public function getSubscribers(Container $container)
    {
        return [$container['router.subscriber']];
    }

}
