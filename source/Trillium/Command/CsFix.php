<?php

/**
 * Part of the Trillium
 *
 * @author  Kilte Leichnam <nwotnbm@gmail.com>
 * @package Trillium
 */

namespace Trillium\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;

/**
 * CsFix Class
 *
 * @package Trillium\Command
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
    protected function configure()
    {
        $this
            ->setDescription('Fix coding standards')
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $process = new Process($this->directory . 'vendor/bin/php-cs-fixer fix ' . $this->directory);
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
