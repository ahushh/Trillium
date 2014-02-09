<?php

/**
 * Part of the Trillium
 *
 * @author  Kilte Leichnam <nwotnbm@gmail.com>
 * @package Trillium
 */

namespace Trillium\Provider;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\Routing\Loader\YamlFileLoader;
use Symfony\Component\Routing\Router;
use Trillium\General\Application;

/**
 * RouterProvider Class
 *
 * @package Trillium\Provider
 */
class RouterProvider
{

    /**
     * Creates the router instance
     *
     * @param Application $app An application instance
     *
     * @return Router
     */
    public function register(Application $app)
    {
        $loader = new YamlFileLoader(new FileLocator($app->configuration->getPaths()));
        $options = [
            'cache_dir'             => $app->getCacheDir(),
            'debug'                 => $app->isDebug(),
            'generator_cache_class' => 'CachedUrlGenerator',
            'matcher_cache_class'   => 'CachedUrlMatcher',
        ];
        $router = new Router($loader, 'routes.yml', $options, null, $app->logger);
        $router->getContext()->setHttpPort($app->configuration->get('request.http_port', 80));
        $router->getContext()->setHttpsPort($app->configuration->get('request.https_port', 443));

        return $router;
    }

}
