<?php

/**
 * Part of the Trillium
 *
 * @package Trillium
 */

namespace Trillium\Model;

use Trillium\MySQLi\MySQLi;

/**
 * Model Class
 *
 * Base class for the models
 *
 * @package Trillium\Model
 */
abstract class Model
{
    /**
     * @var MySQLi instance of the MySQLi class
     */
    protected $db;

    /**
     * Create instance
     * @param MySQLi $db Instance of the MySQLi class
     */
    public function __construct(MySQLi $db)
    {
        $this->db = $db;
    }

}
