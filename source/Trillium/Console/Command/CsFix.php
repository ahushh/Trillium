<?php

/**
 * Part of the Trillium
 *
 * @author  Kilte Leichnam <nwotnbm@gmail.com>
 * @package Trillium
 */

namespace Trillium\Console\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;
use Trillium\Console\CommandInterface;
use Vermillion\Container;

/**
 * CsFix Class
 *
 * @package Trillium\Console\Command
 */
class CsFix implements CommandInterface
{

    /**
     * @var string Path to sources
     */
    private $directory;

    /**
     * {@inheritdoc}
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $process = new Process($this->directory . 'vendor/bin/php-cs-fixer fix ' . $this->directory);
        $status  = $process->run(
            function ($status, $data) use ($output) {
                $output->write($data);
                if ($output->getVerbosity() === OutputInterface::VERBOSITY_VERBOSE) {
                    $output->write($status);
                }
            }
        );

        return $status;
    }

    /**
     * {@inheritdoc}
     */
    public function getArguments()
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function getOptions()
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return 'Fix coding standards';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'cs-fix';
    }

    /**
     * {@inheritdoc}
     */
    public function register(Container $container)
    {
        /** @var $env \Vermillion\Environment */
        $env             = $container['environment'];
        $this->directory = $env->getDirectory('source') . '../';
    }

}
