<?php

/**
 * Part of the Trillium
 *
 * @author  Kilte Leichnam <nwotnbm@gmail.com>
 * @package Trillium
 */

namespace Trillium\Console;

use Symfony\Component\Console\Command\Command as BaseCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Vermillion\Container;

/**
 * Decorator Class
 *
 * @package Trillium\Console
 */
class Command extends BaseCommand
{

    /**
     * @var CommandInterface A command
     */
    private $command;

    /**
     * @var Container A container
     */
    private $container;

    /**
     * Constructor
     *
     * @param CommandInterface $command   A command
     * @param Container        $container A container
     *
     * @throws \LogicException
     *
     * @return self
     */
    public function __construct(CommandInterface $command, Container $container)
    {
        $this->command   = $command;
        $this->container = $container;
        $this->setDescription($this->command->getDescription());
        parent::__construct($this->command->getName());
        $defaults = ['shortcut' => null, 'mode' => null, 'description' => '', 'default' => null];
        foreach ($this->command->getArguments() as $name => $arg) {
            $arg = array_replace($defaults, $arg);
            $this->addArgument($name, $arg['mode'], $arg['description'], $arg['default']);
        }
        foreach ($this->command->getOptions() as $name => $option) {
            $option = array_replace($defaults, $option);
            $this->addOption(
                $name,
                $option['shortcut'],
                $option['mode'],
                $option['description'],
                $option['default']
            );
        }
    }

    /**
     * {@inheritdoc}
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $this->command->register($this->container);

        return $this->command->execute($input, $output);
    }

}
