<?php

/**
 * Part of the Trillium
 *
 * @author  Kilte Leichnam <nwotnbm@gmail.com>
 * @package Trillium
 */

namespace Trillium\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;
use Trillium\General\Console\Command;

/**
 * Environment Class
 *
 * @package Trillium\Command
 */
class Environment extends Command
{

    /**
     * @var array Output messages
     */
    private $messages = [
        'wrong_env'  => '<fg=red>Wrong environment "%s" given</fg=red>',
        'wrong_file' => '<fg=red>Configuration file ".environment" does not exists</fg=red>',
        'success'    => '<info>Environment changed to "%s"</info>',
        'failed'     => '<fg=red>Failed to change environment to "%s"</fg=red>'
    ];

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('env')
            ->setDescription('Change or display environment')
            ->addArgument(
                'environment',
                InputArgument::OPTIONAL,
                'Change or display environment. ' . "\n"
                . 'Available environments: development, testing, production. ' . "\n"
                . 'Leave empty to see the current environment.'
            )
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $env = $input->getArgument('environment');
        if (empty($env)) {
            return $this->displayEnvironment($output);
        } else {
            return $this->changeEnvironment($env, $output);
        }
    }

    /**
     * Returns the path to the configuration file
     *
     * @return boolean|string
     */
    private function getEnvironment()
    {
        return $this->app->getApplicationDir() . '.environment';
    }

    /**
     * Displays the current environment
     *
     * @param OutputInterface $output
     *
     * @return int
     */
    private function displayEnvironment(OutputInterface $output)
    {
        $output->writeln(sprintf('<info>%s</info>', $this->app->getEnvironment()));

        return 0;
    }

    /**
     * Changes the environment
     *
     * @param                 $env
     * @param OutputInterface $output
     *
     * @return int
     */
    private function changeEnvironment($env, OutputInterface $output)
    {
        if (!in_array($env, ['development', 'testing', 'production'])) {
            $output->writeln(sprintf($this->messages['wrong_env'], $env));

            return 1;
        } else {
            $path = $this->getEnvironment();
            if ($path === false) {
                $output->writeln($this->messages['wrong_file']);

                return 1;
            } else {
                $filesystem = new Filesystem();
                $filesystem->dumpFile($path, $env);
                $output->writeln(sprintf($this->messages[is_file($path) ? 'success' : 'failed'], $env));

                return 0;
            }
        }
    }

}
