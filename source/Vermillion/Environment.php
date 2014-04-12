<?php

/**
 * Part of the Vermillion
 *
 * @author  Kilte Leichnam <nwotnbm@gmail.com>
 * @package Vermillion
 */

namespace Vermillion;

use Symfony\Component\Debug\DebugClassLoader;
use Symfony\Component\Debug\ErrorHandler;
use Trillium\Debug\ExceptionHandler;

/**
 * Environment Class
 *
 * @package Vermillion
 */
class Environment
{

    /**
     * @var array List of the all necessary directories
     */
    private $directories = [
        'source'           => 'source',
        'configuration'    => 'resources/configuration',
        'cache'            => 'resources/cache',
        'logs'             => 'resources/logs',
        'views'            => 'resources/views',
        'static.source'    => 'resources/static',
        'static.generated' => 'resources/static/generated',
        'static.cache'     => 'resources/cache/assets',
        'static.public'    => 'public/static',
        'db'               => 'resources/db',
    ];

    /**
     * @var array Available environments
     */
    private $availableEnvironments = [
        'development',
        'production'
    ];

    /**
     * @var string Current environment
     */
    private $environment;

    /**
     * @var boolean Is debug mode enabled?
     */
    private $isDebug = false;

    /**
     * Constructor
     *
     * @throws \RuntimeException
     * @return self
     */
    public function __construct()
    {
        error_reporting(-1);
        date_default_timezone_set('UTC');
        foreach ($this->directories as $key => $directory) {
            $path = realpath(__DIR__ . '/../../' . $directory);
            if ($path === false) {
                throw new \RuntimeException(sprintf('Directory "%s" does not exists'));
            }
            $this->directories[$key] = $path . '/';
        }
        $environment = $this->directories['configuration'] . 'environment';
        if (!is_file($environment)) {
            throw new \RuntimeException(sprintf('Environment configuration file "%s" does not exists', $environment));
        }
        $environment = @file_get_contents($environment);
        if ($environment === false) {
            throw new \RuntimeException(
                sprintf(
                    'Unable to get contents of the environment configuration file: %s',
                    error_get_last()['message']
                )
            );
        }
        if (!in_array($environment, $this->availableEnvironments)) {
            throw new \RuntimeException(sprintf('Environment "%s" is not available', $environment));
        }
        $this->environment = $environment;
        $this->isDebug     = $environment !== 'production';
        if ($this->isDebug) {
            ErrorHandler::register(-1, 1);
            if ('cli' !== php_sapi_name()) {
                ExceptionHandler::register($this->isDebug());
                // CLI - display errors only if they're not already logged to STDERR
            } elseif ((!ini_get('log_errors') || ini_get('error_log'))) {
                ini_set('display_errors', 1);
            }
            DebugClassLoader::enable();
        }
    }

    /**
     * Returns a path to a directory by key
     *
     * @param string $key Key
     *
     * @throws \InvalidArgumentException
     * @return string
     */
    public function getDirectory($key)
    {
        if (!array_key_exists($key, $this->directories)) {
            throw new \InvalidArgumentException(sprintf('Directory "%s" is not defined', $key));
        }

        return $this->directories[$key];
    }

    /**
     * Returns the name of the current environment
     *
     * @return string
     */
    public function getEnvironment()
    {
        return $this->environment;
    }

    /**
     * Is debug mode enabled?
     *
     * @return boolean
     */
    public function isDebug()
    {
        return $this->isDebug;
    }

}
