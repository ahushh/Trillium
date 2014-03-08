<?php

/**
 * Part of the Trillium
 *
 * @author  Kilte Leichnam <nwotnbm@gmail.com>
 * @package Trillium
 */

namespace Trillium\Service\Configuration;

use Symfony\Component\Config\Loader\FileLoader;
use Symfony\Component\Yaml\Parser;

/**
 * YamlFileLoader Class
 *
 * @package Trillium\Service\Configuration
 */
class YamlFileLoader extends FileLoader
{

    /**
     * {@inheritdoc}
     */
    public function load($resource, $type = null)
    {
        $path = $this->locator->locate($resource . '.yml');

        return (new Parser())->parse(file_get_contents($path));
    }

    /**
     * {@inheritdoc}
     */
    public function supports($resource, $type = null)
    {
        return is_string($resource) && (!$type || 'yml' === $type);
    }

}
