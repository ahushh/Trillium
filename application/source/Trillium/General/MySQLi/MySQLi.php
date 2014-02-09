<?php

/**
 * Part of the Trillium
 *
 * @author  Kilte Leichnam <nwotnbm@gmail.com>
 * @package Trillium
 */

namespace Trillium\General\MySQLi;

use Trillium\General\MySQLi\Exception\ConnectionException;
use Trillium\General\MySQLi\Exception\QueryException;

/**
 * MySQLi Class
 *
 * @package Trillium\General\MySQLi
 */
class MySQLi extends \mysqli
{

    /**
     * Constructor
     *
     * @param array $conf Configuration
     *
     * @throws ConnectionException
     * @return self
     */
    public function __construct(array $conf = [])
    {
        $defaults = [
            'host'    => ini_get("mysqli.default_host"),
            'user'    => ini_get("mysqli.default_user"),
            'pass'    => ini_get("mysqli.default_pw"),
            'db'      => '',
            'port'    => ini_get("mysqli.default_port"),
            'socket'  => ini_get("mysqli.default_socket"),
            'charset' => 'utf8',
        ];
        foreach ($defaults as $key => $value) {
            if (!array_key_exists($key, $conf)) {
                $conf[$key] = $value;
            }
        }
        @parent::__construct($conf['host'], $conf['user'], $conf['pass'], $conf['db'], (int) $conf['port'], $conf['socket']);
        if ($this->connect_errno !== 0) {
            throw new ConnectionException($this->connect_error, $this->connect_errno);
        }
        $this->set_charset($conf['charset']);
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
            throw new QueryException($this->error, $statement, $this->error);
        }

        return $result;
    }

}
