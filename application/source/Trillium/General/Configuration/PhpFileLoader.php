<?php

/**
 * Part of the Trillium
 *
 * @author  Kilte Leichname <nwotnbm@gmail.com>
 * @package Trillium
 */

namespace Trillium\General\Configuration;

use Symfony\Component\Config\Loader\FileLoader;

/**
 * PhpFileLoader Class
 *
 * @package Trillium\General\Configuration
 */
class PhpFileLoader extends FileLoader
{

    /**
     * {@inheritdoc}
     */
    public function load($resource, $type = null)
    {
        $path = $this->locator->locate($resource . '.php');

        return include $path;
    }

    /**
     * {@inheritdoc}
     */
    public function supports($resource, $type = null)
    {
        return is_string($resource) && (!$type || 'php' === $type);
    }

}
