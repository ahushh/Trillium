<?php

/**
 * Part of the Trillium
 *
 * @author  Kilte Leichnam <nwotnbm@gmail.com>
 * @package Trillium
 */

namespace Trillium\Service\MySQLi\Exception;

/**
 * QueryException Class
 *
 * @package Trillium\Service\MySQLi\Exception
 */
class QueryException extends MySQLiException
{

    /**
     * @var string SQL Statement
     */
    private $statement;

    /**
     * Constructor
     *
     * @param string     $message   An error message
     * @param string     $statement A SQL statement
     * @param int        $code      A code
     * @param \Exception $previous  A previous exception
     */
    public function __construct($message, $statement, $code, \Exception $previous = null)
    {
        $this->statement = $statement;
        parent::__construct($message, $code, $previous);
    }

    /**
     * Returns a SQL statement
     *
     * @return string
     */
    public function getStatement()
    {
        return $this->statement;
    }

}
