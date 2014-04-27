<?php

/**
 * Part of the Trillium
 *
 * @author  Kilte Leichnam <nwotnbm@gmail.com>
 * @package Trillium
 */

namespace Trillium\Console\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;
use Trillium\Console\CommandInterface;
use Vermillion\Container;

/**
 * Environment Class
 *
 * @package Trillium\Console\Command
 */
class Environment implements CommandInterface
{

    /**
     * @var Filesystem Filesystem instance
     */
    private $fs;

    /**
     * @var array Output messages
     */
    private $messages = [
        'wrong_env'  => '<fg=red>Wrong environment "%s" given</fg=red>',
        'wrong_file' => '<fg=red>Configuration file ".environment" does not exists</fg=red>',
        'success'    => '<info>Environment changed to "%s"</info>',
        'failed'     => '<fg=red>Failed to change environment to "%s"</fg=red>',
        'info'       => "Current: %s\nAvailable: %s",
    ];

    /**
     * @var string Path to the environment config
     */
    private $environmentPath;

    /**
     * @var string Current application environment
     */
    private $currentEnvironment;

    /**
     * @var array List of available environments
     */
    private $availableEnvironments = ['development', 'production'];

    /**
     * {@inheritdoc}
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $env    = $input->getArgument('environment');
        $status = 0;
        if (empty($env)) {
            $output->writeln(
                sprintf(
                    $this->messages['info'],
                    $this->currentEnvironment,
                    implode(', ', $this->availableEnvironments)
                )
            );
        } else {
            if (!in_array($env, $this->availableEnvironments)) {
                $output->writeln(sprintf($this->messages['wrong_env'], $env));
                $status = 1;
            } else {
                $path = realpath($this->environmentPath);
                if ($path === false) {
                    $output->writeln($this->messages['wrong_file']);
                    $status = 1;
                } else {
                    $this->fs->dumpFile($path, $env);
                    $output->writeln(sprintf($this->messages[is_file($path) ? 'success' : 'failed'], $env));
                }
            }
        }

        return $status;
    }

    /**
     * {@inheritdoc}
     */
    public function getArguments()
    {
        return [
            'environment' => [
                'mode'        => InputArgument::OPTIONAL,
                'description' => 'Change or display environment. ' . "\n"
                    . 'Available environments: ' . implode(', ', $this->availableEnvironments) . '. ' . "\n"
                    . 'Leave empty to see the current environment.'
            ]
        ];
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
        return 'Change/display environment';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'env';
    }

    /**
     * {@inheritdoc}
     */
    public function register(Container $container)
    {
        /** @var $env \Vermillion\Environment */
        $env                      = $container['environment'];
        $this->environmentPath    = $env->getDirectory('configuration') . 'environment';
        $this->currentEnvironment = $env->getEnvironment();
        $this->fs                 = $container['filesystem'];
    }

}
