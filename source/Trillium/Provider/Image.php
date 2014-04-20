<?php

/**
 * Part of the Trillium
 *
 * @author  Kilte Leichnam <nwotnbm@gmail.com>
 * @package Trillium
 */

namespace Trillium\Provider;

use Symfony\Component\Filesystem\Filesystem;
use Trillium\Service\Image\Manager;
use Trillium\Service\Image\Resize;
use Trillium\Service\Image\Validator;
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
     * @var array Configuration for services
     */
    private $configuration = [];

    /**
     * {@inheritdoc}
     */
    public function registerServices(Container $container)
    {
        $container['imageValidator'] = function ($c) {
            return new Validator($this->getConfiguration($c));
        };
        $container['imageResize']    = function () {
            return new Resize();
        };
        $container['imageManager']   = function ($c) {
            return new Manager(new Filesystem(), $c['imageResize'], $this->getConfiguration($c));
        };
    }

    /**
     * Returns the configuration for services
     *
     * @param Container $c Container Container instance
     *
     * @return array
     */
    private function getConfiguration(Container $c)
    {
        if (empty($this->configuration)) {
            /**
             * @var $configuration \Vermillion\Configuration\Configuration
             * @var $environment   \Vermillion\Environment
             */
            $configuration                    = $c['configuration'];
            $environment                      = $c['environment'];
            $this->configuration              = $configuration->load('image')->get();
            $this->configuration['directory'] = $environment->getDirectory('images');
        }

        return $this->configuration;
    }

}
