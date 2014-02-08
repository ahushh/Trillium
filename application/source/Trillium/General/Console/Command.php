<?php

/**
 * Part of the Trillium
 *
 * @author  Kilte Leichnam <nwotnbm@gmail.com>
 * @package Trillium
 */

namespace Trillium\General\Console;

use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Trillium\General\Application as Trillium;

/**
 * Command Class
 *
 * @package Trillium\General\Console
 */
class Command extends SymfonyCommand
{

    /**
     * @var Trillium An application instance
     */
    protected $app;

    /**
     * {@inheritdoc}
     * @param Trillium $app An application instance
     */
    public function __construct(Trillium $app)
    {
        $this->app = $app;
        parent::__construct();
    }

}
