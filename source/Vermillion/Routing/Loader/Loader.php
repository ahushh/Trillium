<?php

/**
 * Part of the Vermillion
 *
 * @author  Kilte Leichnam <nwotnbm@gmail.com>
 * @package Vermillion
 */

namespace Vermillion\Routing\Loader;

use Symfony\Component\Config\Resource\FileResource;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

/**
 * Loader Class
 *
 * Based on the Symfony Routing YamlFileLoader
 *
 * @author  Fabien Potencier <fabien@symfony.com>
 * @author  Tobias Schultze <http://tobion.de>
 *
 * @package Vermillion\Routing\Loader
 */
abstract class Loader extends \Vermillion\Configuration\Loader\Loader
{

    private static $availableKeys = [
        'resource',
        'type',
        'prefix',
        'pattern',
        'path',
        'host',
        'schemes',
        'methods',
        'defaults',
        'requirements',
        'options',
        'condition'
    ];

    /**
     * Creates a RouteCollection
     *
     * @param array  $config Configuration
     * @param string $path   Path to the configuration file
     *
     * @throws \InvalidArgumentException
     * @return RouteCollection
     */
    protected function createCollection(array $config, $path)
    {
        $collection = new RouteCollection();
        $collection->addResource(new FileResource($path));
        if (empty($config)) {
            return $collection;
        }
        foreach ($config as $name => $route) {
            if (isset($route['pattern'])) {
                if (isset($route['path'])) {
                    throw new \InvalidArgumentException(sprintf(
                        'The file "%s" cannot define both a "path" and a "pattern" attribute. Use only "path".',
                        $path
                    ));
                }
                $route['path'] = $route['pattern'];
                unset($route['pattern']);
            }
            $this->validate($route, $name, $path);
            $collection->add($name, $this->createRoute($route));
        }

        return $collection;
    }

    /**
     * Creates a route and adds it to the RouteCollection.
     *
     * @param array $config Route definition
     *
     * @return Route
     */
    protected function createRoute(array $config)
    {
        return new Route(
            $config['path'],
            isset($config['defaults']) ? $config['defaults'] : [],
            isset($config['requirements']) ? $config['requirements'] : [],
            isset($config['options']) ? $config['options'] : [],
            isset($config['host']) ? $config['host'] : '',
            isset($config['schemes']) ? $config['schemes'] : [],
            isset($config['methods']) ? $config['methods'] : [],
            isset($config['condition']) ? $config['condition'] : null
        );
    }

    /**
     * Validates the route configuration.
     *
     * @param array  $config A resource config
     * @param string $name   The config key
     * @param string $path   The loaded file path
     *
     * @throws \InvalidArgumentException If one of the provided config keys is not supported,
     *                                   something is missing or the combination is nonsense
     */
    protected function validate($config, $name, $path)
    {
        if (!is_array($config)) {
            throw new \InvalidArgumentException(sprintf(
                'The definition of "%s" in "%s" must be an array.',
                $name,
                $path
            ));
        }
        if ($extraKeys = array_diff(array_keys($config), self::$availableKeys)) {
            throw new \InvalidArgumentException(sprintf(
                'The routing file "%s" contains unsupported keys for "%s": "%s". Expected one of: "%s".',
                $path,
                $name,
                implode('", "', $extraKeys),
                implode('", "', self::$availableKeys)
            ));
        }
        if (isset($config['resource']) && isset($config['path'])) {
            throw new \InvalidArgumentException(sprintf(
                'The routing file "%s" must not specify both the "resource" key and the "path" key for "%s". Choose between an import and a route definition.',
                $path,
                $name
            ));
        }
        if (!isset($config['resource']) && isset($config['type'])) {
            throw new \InvalidArgumentException(sprintf(
                'The "type" key for the route definition "%s" in "%s" is unsupported. It is only available for imports in combination with the "resource" key.',
                $name,
                $path
            ));
        }
        if (!isset($config['resource']) && !isset($config['path'])) {
            throw new \InvalidArgumentException(sprintf(
                'You must define a "path" for the route "%s" in file "%s".',
                $name,
                $path
            ));
        }
    }

}
