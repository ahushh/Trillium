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
use Trillium\General\Application;

/**
 * Environment Class
 *
 * @package Trillium\Command
 */
class Environment extends Command
{

    /**
     * @var Application An application instance
     */
    private $app;

    /**
     * {@inheritdoc}
     * @param Application $app An application instance
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
        parent::__construct('env');
        $this->setDescription('Change or display environment');
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
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
        $output->writeln('<info>' . $this->app->getEnvironment() . '</info>');
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

}
