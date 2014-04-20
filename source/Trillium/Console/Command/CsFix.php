<?php

/**
 * Part of the Trillium
 *
 * @author  Kilte Leichnam <nwotnbm@gmail.com>
 * @package Trillium
 */

namespace Trillium\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;

/**
 * CsFix Class
 *
 * @package Trillium\Console\Command
 */
class CsFix extends Command
{

    /**
     * @var string Path to sources
     */
    private $directory;

    /**
     * Constructor
     *
     * @param string $directory Path to sources
     *
     * @throws \LogicException
     * @throws \InvalidArgumentException
     * @return self
     */
    public function __construct($directory)
    {
        $this->directory = $directory;
        parent::__construct('cs-fix');
    }

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
    protected function configure()
    {
        $this->setDescription('Fix coding standards');
    }

}
