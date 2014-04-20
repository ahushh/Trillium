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

/**
 * Db Class
 *
 * @package Trillium\Console\Command
 */
class Db extends Command
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
     * Constructor
     *
     * @param array   $dumps  Paths to sql files
     * @param \mysqli $mysqli MySQLi instance
     *
     * @throws \InvalidArgumentException
     *
     * @return self
     */
    public function __construct(array $dumps, \mysqli $mysqli)
    {
        $this->mysqli = $mysqli;
        foreach ($dumps as $dump) {
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
        parent::__construct('db');
    }

    /**
     * {@inheritdoc}
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $errors = [];
        foreach ($this->statements as $statement) {
            try {
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
    protected function configure()
    {
        $this->setDescription('Loads databases from dump files');
    }

}
