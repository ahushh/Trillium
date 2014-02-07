<?php

/**
 * Part of the Trillium
 *
 * @author  Kilte Leichnam <nwotnbm@gmail.com>
 * @package Trillium
 */

namespace Trillium\General\Console;

use Symfony\Component\Console\Application as SymfonyApplication;
use Trillium\Command\Environment;
use Trillium\General\Application as Trillium;

/**
 * Application Class
 *
 * @package Trillium\General\Console
 */
class Application extends SymfonyApplication
{

    /**
     * {@inheritdoc}
     */
    public function __construct()
    {
        parent::__construct('Trillium', Trillium::VERSION);
    }

    /**
     * {@inheritdoc}
     */
    protected function getDefaultCommands()
    {
        $commands = parent::getDefaultCommands();
        $commands[] = new Environment();

        return $commands;
    }

}
