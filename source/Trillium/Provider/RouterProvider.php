<?php

/**
 * Part of the Trillium
 *
 * @author  Kilte Leichnam <nwotnbm@gmail.com>
 * @package Trillium
 */

namespace Trillium\Provider;

use Psr\Log\LoggerInterface;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Routing\Loader\YamlFileLoader;
use Symfony\Component\Routing\Router;

/**
 * RouterProvider Class
 *
 * @package Trillium\Provider
 */
class RouterProvider
{

    /**
     * @var Router Router
     */
    private $router;

    /**
     * Constructor
     *
     * @param array           $paths     Paths to the configuration files
     * @param string          $config    Name of the configuration file
     * @param int             $httpPort  HTTP port
     * @param int             $httpsPort HTTPS port
     * @param string          $cache     Path to the cache directory
     * @param boolean         $debug     Is debug
     * @param LoggerInterface $logger    Logger instance
     *
     * @return self
     */
    public function __construct(array $paths, $config, $httpPort, $httpsPort, $cache, $debug, LoggerInterface $logger = null)
    {
        $loader = new YamlFileLoader(new FileLocator($paths));
        $options = [
            'cache_dir'             => $cache,
            'debug'                 => $debug,
            'generator_cache_class' => 'CachedUrlGenerator',
            'matcher_cache_class'   => 'CachedUrlMatcher',
        ];
        $this->router = new Router($loader, $config . '.yml', $options, null, $logger);
        $this->router->getContext()->setHttpPort($httpPort);
        $this->router->getContext()->setHttpsPort($httpsPort);
    }

    /**
     * Returns router instance
     *
     * @return Router
     */
    public function router()
    {
        return $this->router;
    }

}
