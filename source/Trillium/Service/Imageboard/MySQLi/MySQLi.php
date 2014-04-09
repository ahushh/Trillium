<?php

/**
 * Part of the Trillium
 *
 * @author  Kilte Leichnam <nwotnbm@gmail.com>
 * @package Trillium
 */

namespace Trillium\Service\Imageboard\MySQLi;

/**
 * MySQLi Class
 *
 * @package Trillium\Service\Imageboard\MySQLi
 */
abstract class MySQLi
{

    /**
     * @var \mysqli MySQLi instance
     */
    protected $mysqli;

    /**
     * @var string Name of the table in database
     */
    protected $tableName;

    /**
     * Constructor
     *
     * @param \mysqli $mysqli    MySQLi instance
     * @param string  $tableName Name of the table in database
     *
     * @return self
     */
    public function __construct(\mysqli $mysqli, $tableName)
    {
        $this->mysqli    = $mysqli;
        $this->tableName = $tableName;
    }

}
