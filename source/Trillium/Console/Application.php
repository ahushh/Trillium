<?php

/**
 * Part of the Trillium
 *
 * @author  Kilte Leichnam <nwotnbm@gmail.com>
 * @package Trillium
 */

namespace Trillium\Console;

use Trillium\Console\Command\Assets;
use Trillium\Console\Command\CsFix;
use Trillium\Console\Command\Environment;
use Trillium\Console\Command\JsUrlGenerator;
use Vermillion\Container;

/**
 * Application Class
 *
 * @package Trillium\Console
 */
class Application extends \Symfony\Component\Console\Application
{

    /**
     * @var \Vermillion\Environment
     */
    private $env;

    /**
     * @var \Symfony\Component\Routing\Router
     */
    private $router;

    /**
     * @var \Vermillion\Configuration\Configuration
     */
    private $configuration;

    /**
     * {@inheritdoc}
     */
    public function __construct(Container $container)
    {
        $this->env           = $container['environment'];
        $this->router        = $container['router'];
        $this->configuration = $container['configuration'];
        parent::__construct('Trillium', \Vermillion\Application::VERSION);
    }

    /**
     * {@inheritdoc}
     */
    protected function getDefaultCommands()
    {
        return array_merge(
            parent::getDefaultCommands(),
            [
                new Assets(
                    $this->env->getDirectory('static.source'),
                    $this->env->getDirectory('static.public'),
                    $this->env->getDirectory('static.cache'),
                    $this->configuration->load('assets')->get()
                ),
                new CsFix($this->env->getDirectory('source') . '../'),
                new Environment(
                    $this->env->getDirectory('configuration') . 'environment',
                    $this->env->getEnvironment()
                ),
                new JsUrlGenerator(
                    $this->env->getDirectory('static.source'),
                    $this->router->getRouteCollection()->all()
                ),
            ]
        );
    }

}
