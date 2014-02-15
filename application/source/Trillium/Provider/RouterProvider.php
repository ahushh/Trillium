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
     * @var array Paths to the configuration files
     */
    private $paths;

    /**
     * @var string Name of the configuration file
     */
    private $config;

    /**
     * @var int HTTP port
     */
    private $httpPort;

    /**
     * @var int HTTPS port
     */
    private $httpsPort;

    /**
     * @var string Path to the cache directory
     */
    private $cache;

    /**
     * @var boolean Is debug
     */
    private $debug;

    /**
     * @var LoggerInterface|null Logger instance
     */
    private $logger;

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
        $this->paths     = $paths;
        $this->config    = $config;
        $this->httpPort  = $httpPort;
        $this->httpsPort = $httpsPort;
        $this->cache     = $cache;
        $this->debug     = $debug;
        $this->logger    = $logger;
    }

    /**
     * Returns router instance
     *
     * @return Router
     */
    public function router()
    {
        if ($this->router === null) {
            $loader = new YamlFileLoader(new FileLocator($this->paths));
            $options = [
                'cache_dir'             => $this->cache,
                'debug'                 => $this->debug,
                'generator_cache_class' => 'CachedUrlGenerator',
                'matcher_cache_class'   => 'CachedUrlMatcher',
            ];
            $this->router = new Router($loader, $this->config . '.yml', $options, null, $this->logger);
            $this->router->getContext()->setHttpPort($this->httpPort);
            $this->router->getContext()->setHttpsPort($this->httpsPort);
        }

        return $this->router;
    }

}
