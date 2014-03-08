<?php

/**
 * Part of the Trillium
 *
 * @author  Kilte Leichnam <nwotnbm@gmail.com>
 * @package Trillium
 */

namespace Trillium\Service\MySQLi;

use Trillium\Service\MySQLi\Exception\ConnectionException;
use Trillium\Service\MySQLi\Exception\QueryException;

/**
 * MySQLi Class
 *
 * @package Trillium\Service\MySQLi
 */
class MySQLi extends \mysqli
{

    /**
     * Constructor
     *
     * Configuration parameters:
     * <pre>
     * $conf = [
     *     host   => 'localhost'
     *     user   => 'root'
     *     pass   => 'password'
     *     db     => 'database'
     *     port   => ini_get("mysqli.default_port")
     *     socket => ini_get("mysqli.default_socket")
     * ];
     * </pre>
     *
     * @param array $conf Configuration
     *
     * @throws ConnectionException
     * @return self
     */
    public function __construct(array $conf)
    {
        @parent::__construct(
            $conf['host'],
            $conf['user'],
            $conf['pass'],
            $conf['db'],
            (int) $conf['port'],
            $conf['socket']
        );
        if ($this->connect_errno !== 0) {
            throw new ConnectionException($this->connect_error, $this->connect_errno);
        }
    }

    /**
     * Run Query
     *
     * @param string $statement SQL statement
     * @param int    $type      Result type (MYSQLI_USE_RESULT or MYSQLI_STORE_RESULT)
     *
     * @throws QueryException
     * @return \mysqli_result
     */
    public function query($statement, $type = MYSQLI_USE_RESULT)
    {
        $result = parent::query($statement, $type);
        if (!empty($this->error)) {
            throw new QueryException($this->error, $statement, $this->errno);
        }

        return $result;
    }

}
