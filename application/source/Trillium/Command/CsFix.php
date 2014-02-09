<?php

/**
 * Part of the Trillium
 *
 * @author  Kilte Leichnam <nwotnbm@gmail.com>
 * @package Trillium
 */

namespace Trillium\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;
use Trillium\General\Console\Command;

/**
 * CsFix Class
 *
 * @package Trillium\Command
 */
class CsFix extends Command
{

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('cs-fix')
            ->setDescription('Fix coding standards')
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $rootDir = realpath($this->app->getApplicationDir() . '../') . '/';
        $process = new Process($rootDir . 'vendor/bin/php-cs-fixer fix ' . $rootDir);
        $status = $process->run(
            function ($status, $data) use ($output) {
                $output->write($data);
                if ($output->getVerbosity() === OutputInterface::VERBOSITY_VERBOSE) {
                    $output->write($status);
                }
            }
        );

        return $status;
    }

}
