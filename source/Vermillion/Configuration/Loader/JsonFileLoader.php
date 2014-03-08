<?php

/**
 * Part of the Vermillion
 *
 * @author  Kilte Leichnam <nwotnbm@gmail.com>
 * @package Vermillion
 */

namespace Vermillion\Configuration\Loader;

/**
 * JsonFileLoader Class
 *
 * @package Vermillion\Configuration\Loader
 */
class JsonFileLoader extends Loader
{

    /**
     * {@inheritdoc}
     */
    public function load($resource, $type = null)
    {
        return json_decode(file_get_contents($this->locator->locate($resource)), true);
    }

    /**
     * {@inheritdoc}
     */
    public function supports($resource, $type = null)
    {
        return pathinfo($this->locator->locate($resource), PATHINFO_EXTENSION) === 'json';
    }

}
