<?php

/**
 * Part of the Trillium
 *
 * @author  Kilte Leichname <nwotnbm@gmail.com>
 * @package Trillium
 */

namespace Trillium\General\Configuration;

use Symfony\Component\Config\FileLocator;

/**
 * Configuration Class
 *
 * @package Trillium\General\Configuration
 */
class Configuration
{

    /**
     * Path to the configuration directory
     */
    const DIRECTORY = '/../../../../resources/configuration/';

    /**
     * @var array Configuration values
     */
    private $configuration;

    /**
     * @var array Paths to the configuration files
     */
    private $paths;

    /**
     * Constructor
     *
     * @param string $environment Application environment
     *
     * @return self
     */
    public function __construct($environment)
    {
        $this->configuration = [];
        $confDir = __DIR__ . self::DIRECTORY;
        $this->paths = [
            $confDir . $environment . '/',
            $confDir . 'default/',
        ];
        $loader = new PhpFileLoader(new FileLocator($this->paths));
        $this->configuration = $loader->load('application');
    }

    /**
     * Gets value by key
     *
     * @param string|null $key     Key
     * @param mixed|null  $default Default value, if key is not exists
     *
     * @return null
     */
    public function get($key = null, $default = null)
    {
        return $this->has($key) ? $this->configuration[$key] : $default;
    }

    /**
     * Checks, whether value exists
     *
     * @param string $key Key
     *
     * @return boolean
     */
    public function has($key)
    {
        return array_key_exists($key, $this->configuration);
    }

    /**
     * Returns list of paths to the configuration files
     * You can to use it as paths for a file locator
     *
     * @return array
     */
    public function getPaths()
    {
        return $this->paths;
    }

}
