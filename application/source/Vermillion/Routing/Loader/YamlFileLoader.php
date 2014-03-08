<?php

/**
 * Part of the Vermillion
 *
 * @author  Kilte Leichnam <nwotnbm@gmail.com>
 * @package Vermillion
 */

namespace Vermillion\Routing\Loader;

/**
 * YamlFileLoader Class
 *
 * @package Vermillion\Routing\Loader
 */
class YamlFileLoader extends \Symfony\Component\Routing\Loader\YamlFileLoader
{

    /**
     * {@inheritdoc}
     */
    public function supports($resource, $type = null)
    {
        return pathinfo($this->locator->locate($resource), PATHINFO_EXTENSION) === 'yml';
    }

}
