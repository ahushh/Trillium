<?php

/**
 * Part of the Trillium
 *
 * @author  Kilte Leichnam <nwotnbm@gmail.com>
 * @package Trillium
 */

namespace Trillium\Provider;

use Symfony\Component\Filesystem\Filesystem as FilesystemService;
use Vermillion\Container;
use Vermillion\Provider\ServiceProviderInterface;

/**
 * Filesystem Class
 *
 * @package Trillium\Provider
 */
class Filesystem implements ServiceProviderInterface
{

    /**
     * {@inheritdoc}
     */
    public function registerServices(Container $container)
    {
        $container['filesystem'] = function () {
            return new FilesystemService();
        };
    }

}
