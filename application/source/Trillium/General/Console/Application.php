<?php

/**
 * Part of the Trillium
 *
 * @author  Kilte Leichnam <nwotnbm@gmail.com>
 * @package Trillium
 */

namespace Trillium\General\Console;

use Symfony\Component\Console\Application as SymfonyApplication;
use Trillium\Command\Assets;
use Trillium\Command\CsFix;
use Trillium\Command\Environment;
use Trillium\Command\JsUrlGenerator;
use Trillium\General\Application as Trillium;

/**
 * Application Class
 *
 * @package Trillium\General\Console
 */
class Application extends SymfonyApplication
{

    /**
     * @var Trillium A general application instance
     */
    protected $app;

    /**
     * {@inheritdoc}
     * @param Trillium $app A general application instance
     */
    public function __construct(Trillium $app)
    {
        $this->app = $app;
        parent::__construct('Trillium', Trillium::VERSION);
    }

    /**
     * {@inheritdoc}
     */
    protected function getDefaultCommands()
    {
        $commands   = parent::getDefaultCommands();
        $commands[] = new Environment(
            $this->app->getDirectory('application') . '.environment',
            $this->app->getEnvironment()
        );
        $commands[] = new Assets(
            $this->app->getDirectory('assets.source'),
            $this->app->getDirectory('assets.public'),
            $this->app->configuration->load('assets', 'yml')->get()
        );
        $commands[] = new JsUrlGenerator($this->app);
        $commands[] = new CsFix(realpath($this->app->getDirectory('application') . '../') . '/');

        return $commands;
    }

}
