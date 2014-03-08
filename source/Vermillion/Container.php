<?php

/**
 * Part of the Vermillion
 *
 * @author  Kilte Leichnam <nwotnbm@gmail.com>
 * @package Vermillion
 */

namespace Vermillion;

use Symfony\Component\Config\Loader\DelegatingLoader;
use Symfony\Component\Config\Loader\LoaderResolver;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Yaml\Parser;
use Vermillion\Configuration\Configuration;
use Vermillion\Configuration\Loader\JsonFileLoader;
use Vermillion\Configuration\Loader\YamlFileLoader;
use Vermillion\Configuration\Locator\FileLocator;

/**
 * Container Class
 *
 * @package Vermillion
 */
class Container extends \Pimple
{

    /**
     * {@inheritdoc}
     */
    public function __construct()
    {
        parent::__construct();
        $this['logger']                = null;
        $this['environment']           = new Environment();
        $this['configuration.paths']   = [
            $this['environment']->getDirectory('configuration') . $this['environment']->getEnvironment() . '/',
            $this['environment']->getDirectory('configuration') . 'default/'
        ];
        $this['configuration.locator'] = function ($container) {
            return new FileLocator($container['configuration.paths'], ['json', 'yml']);
        };
        $this['configuration.loader']  = function ($container) {
            return new DelegatingLoader(
                new LoaderResolver([
                    new YamlFileLoader(
                        $container['configuration.locator'],
                        $container['yaml']
                    ),
                    new JsonFileLoader(
                        $container['configuration.locator']
                    )
                ])
            );
        };
        $this['configuration']         = function ($container) {
            $configuration = new Configuration($container['configuration.loader']);
            $configuration->setDefault('application');

            return $configuration;
        };
        $this['dispatcher']            = function () {
            return new EventDispatcher();
        };
        $this['yaml']                  = function () {
            return new Parser();
        };
        $this['requestStack']          = function () {
            return new RequestStack();
        };
    }

}
