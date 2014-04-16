<?php

/**
 * Part of the Trillium
 *
 * @author  Kilte Leichnam <nwotnbm@gmail.com>
 * @package Trillium
 */

namespace Trillium\Provider;

use Vermillion\Container;
use Vermillion\Provider\ServiceProviderInterface;

/**
 * Image Class
 *
 * @package Trillium\Provider
 */
class Image implements ServiceProviderInterface
{

    /**
     * {@inheritdoc}
     */
    public function registerServices(Container $container)
    {
        $container['imageService'] = function ($c) {
            /** @var $configuration \Vermillion\Configuration\Configuration */
            $configuration = $c['configuration'];
            $config        = $configuration->load('image')->get();
            /** @var $env \Vermillion\Environment */
            $env = $c['environment'];

            return new \Trillium\Service\Image\Image(
                $env->getDirectory('images'),
                $env->getDirectory('images'),
                $config['max_size'],
                $config['max_width'],
                $config['max_height'],
                $config['thumb_width'],
                $config['thumb_height'],
                $config['thumb_quality']
            );
        };
    }

}
