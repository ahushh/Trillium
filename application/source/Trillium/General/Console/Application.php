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
        $commands = parent::getDefaultCommands();
        $commands[] = new Environment($this->app);
        $commands[] = new Assets($this->app);
        $commands[] = new JsUrlGenerator($this->app);
        $commands[] = new CsFix($this->app);

        return $commands;
    }

}
