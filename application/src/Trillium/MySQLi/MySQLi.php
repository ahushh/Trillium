<?php

/**
 * Part of the Trillium
 *
 * @package Trillium
 */

namespace Trillium\MySQLi;

/**
 * MySQLi Class
 *
 * Represents a connection between PHP and a MySQL database.
 *
 * @package Trillium\MySQLi
 */
class MySQLi extends \mysqli {

    /**
     * Run Query
     *
     * @param string $statement SQL statement
     * @param int    $type      Result Mode MYSQLI_USE_RESULT or MYSQLI_STORE_RESULT
     *
     * @throws \RuntimeException
     * @return \mysqli_result
     */
    public function query($statement, $type = MYSQLI_USE_RESULT) {
        $result = parent::query($statement, $type);
        if (!empty($this->error)) {
            throw new \RuntimeException('Error: ' . $this->error . "\r\n\t" . 'Statement: ' . $statement, $this->errno);
        }
        return $result;
    }

} 