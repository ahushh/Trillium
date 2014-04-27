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
use Trillium\Console\CommandInterface;
use Vermillion\Container;

/**
 * Db Class
 *
 * @package Trillium\Console\Command
 */
class Db implements CommandInterface
{

    /**
     * @var array SQL statements list to execute
     */
    private $statements;

    /**
     * @var \mysqli MySQLi instance
     */
    private $mysqli;

    /**
     * @var string Name of the database
     */
    private $db;

    /**
     * {@inheritdoc}
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $errors  = [];
        $verbose = $output->getVerbosity() == OutputInterface::VERBOSITY_VERBOSE;
        array_unshift(
            $this->statements,
            sprintf("DROP DATABASE IF EXISTS `%s`", $this->db),
            sprintf("CREATE DATABASE `%s`", $this->db),
            sprintf("USE `%s`", $this->db)
        );
        foreach ($this->statements as $statement) {
            try {
                if ($verbose) {
                    $output->writeln(sprintf("\n%s;\n", $statement));
                }
                $this->mysqli->query($statement);
            } catch (\Exception $e) {
                $errors[] = sprintf('[%s] %s', $e->getCode(), $e->getMessage());
            }
        }
        if (!empty($errors)) {
            array_unshift($errors, '<fg=red>[FAIL]</fg=red> The following errors are occurred: ');
            $status = 1;
            $output->writeln($errors);
        } else {
            $output->writeln('<fg=green>[OK]</fg=green>');
            $status = 0;
        }

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
        return 'Loads SQL dumps';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'db';
    }

    /**
     * {@inheritdoc}
     */
    public function register(Container $container)
    {
        /**
         * @var $env    \Vermillion\Environment
         * @var $config \Vermillion\Configuration\Configuration
         */
        $env       = $container['environment'];
        $directory = $env->getDirectory('db') . 'mysqli';
        $dumps     = array_filter(
            array_diff(scandir($directory), ['.', '..']),
            function ($item) use ($directory) {
                return pathinfo($directory . $item, PATHINFO_EXTENSION) === 'sql' ? $item : null;
            }
        );
        if (empty($dumps)) {
            exit('No dumps found');
        }
        $config   = $container['configuration'];
        $conf     = $config->load('mysqli')->get();
        $this->db = $conf['db'];
        unset($conf['db']);
        $this->mysqli = $container['mysqli.factory']($conf);
        foreach ($dumps as $dump) {
            $dump = $directory . '/' . $dump;
            if (!is_file($dump)) {
                throw new \InvalidArgumentException(sprintf('Dump "%s" is not exists', $dump));
            }
            $dump = explode(';', file_get_contents($dump));
            foreach ($dump as $key => $statement) {
                $statement = trim($statement);
                if (empty($statement)) {
                    unset($dump[$key]);
                } else {
                    $this->statements[] = $statement;
                }
            }
            if (empty($dump)) {
                throw new \InvalidArgumentException('Dump is empty');
            }
        }
    }

}
