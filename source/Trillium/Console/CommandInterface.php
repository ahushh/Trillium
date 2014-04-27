<?php

/**
 * Part of the Trillium
 *
 * @author  Kilte Leichnam <nwotnbm@gmail.com>
 * @package Trillium
 */

namespace Trillium\Console;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Vermillion\Container;

/**
 * CommandInterface Interface
 *
 * @package Trillium\Console
 */
interface CommandInterface
{

    /**
     * Returns list of arguments
     *
     * Example:
     * <pre>
     * 'arg_name' => [
     *  'mode'        => InputArgument::REQUIRED,
     *  'description' => 'description',
     *  'default'     => 'default_value'
     * ]
     * </pre>
     *
     * @see \Symfony\Component\Console\Command\Command::addArgument()
     *
     * @return array
     */
    public function getArguments();

    /**
     * Returns list of options
     *
     * Example:
     * <pre>
     * 'option_name' => [
     *  'shortcut'    => 'o',
     *  'mode'        => InputOption::VALUE_REQUIRED,
     *  'description' => 'description',
     *  'default'     => 'default_value'
     * ]
     * </pre>
     *
     * @see \Symfony\Component\Console\Command\Command::addOption()
     *
     * @return array
     */
    public function getOptions();

    /**
     * Returns command description
     *
     * @see \Symfony\Component\Console\Command\Command::setDescription()
     *
     * @return string
     */
    public function getDescription();

    /**
     * Returns command name
     *
     * @see \Symfony\Component\Console\Command\Command::setName()
     *
     * @return string
     */
    public function getName();

    /**
     * Performs before the current command will be executed
     *
     * You can use it to get a services from the container.
     *
     * @param Container $container A container instance
     *
     * @return void
     */
    public function register(Container $container);

    /**
     * Executes the current command
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @see \Symfony\Component\Console\Command\Command::execute()
     *
     * @return mixed
     */
    public function execute(InputInterface $input, OutputInterface $output);

}
