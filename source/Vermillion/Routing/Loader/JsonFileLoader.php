<?php

/**
 * Part of the Vermillion
 *
 * @author  Kilte Leichnam <nwotnbm@gmail.com>
 * @package Vermillion
 */

namespace Vermillion\Routing\Loader;

/**
 * JsonFileLoader Class
 *
 * @package Vermillion\Routing\Loader
 */
class JsonFileLoader extends Loader
{

    /**
     * {@inheritdoc}
     */
    public function load($resource, $type = null)
    {
        $config = json_decode(file_get_contents($path = $this->locator->locate($resource)), true);
        if (!is_array($config)) {
            throw new \RuntimeException(sprintf('Resource "%s" contains invalid json', $resource));
        }

        return $this->createCollection($config, $path);
    }

    /**
     * {@inheritdoc}
     */
    public function supports($resource, $type = null)
    {
        return pathinfo($this->locator->locate($resource), PATHINFO_EXTENSION) === 'json';
    }

}
