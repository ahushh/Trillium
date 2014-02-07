<?php

/**
 * Part of the Trillium
 *
 * @author  Kilte Leichnam <nwotnbm@gmail.com>
 * @package Trillium
 */

namespace Trillium\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Environment Class
 *
 * @package Trillium\Command
 */
class Environment extends Command
{

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('env')
            ->setDescription('Change environment')
            ->addArgument(
                'environment',
                InputArgument::REQUIRED,
                'Change environment. Available environments: development, testing, production'
            )
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $env = $input->getArgument('environment');
        if (!in_array($env, ['development', 'testing', 'production'])) {
            $output->writeln('<error>Wrong environment "' . $env . '" given</error>');
            return 1;
        } else {
            $path = $this->getEnvironment();
            if ($path === false) {
                $output->writeln('<error>Configuration file ".environment" does not exists</error>');
                return 1;
            } else {
                file_put_contents($path, $env);
                $output->writeln('<info>Environment changed to "' . $env . '"</info>');
                return 0;
            }
        }
    }

    /**
     * Returns the path to the configuration file
     *
     * @return boolean|string
     */
    private function getEnvironment()
    {
        return realpath(__DIR__ . '/../../../.environment');
    }

}
