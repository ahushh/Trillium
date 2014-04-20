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

    /**
     * Whether item exists
     *
     * @param string     $key        A key
     * @param string|int $identifier A value
     *
     * @return boolean
     */
    protected function isItemExists($key, $identifier)
    {
        $result = $this->mysqli->query(
            sprintf(
                "SELECT COUNT(*) FROM `%s` WHERE `%s` = '%s'",
                $this->tableName,
                $key,
                $this->escape($identifier)
            )
        );
        $total  = (int) $result->fetch_row()[0];
        $result->free();

        return $total > 0;
    }

    /**
     * Returns an escaped value
     *
     * @param mixed $value A value
     *
     * @return int|string
     */
    protected function escape($value)
    {
        if (is_string($value)) {
            $value = $this->mysqli->real_escape_string($value);
        } else {
            $value = (int) $value;
        }

        return $value;
    }

    /**
     * Returns a list
     *
     * @param string $statement SQL Statement
     *
     * @return array
     */
    protected function listingItems($statement)
    {
        $list   = [];
        $result = $this->mysqli->query($statement);
        while (($board = $result->fetch_assoc())) {
            $list[] = $board;
        }
        $result->free();

        return $list;
    }

    /**
     * Removes an item
     * Returns number of affected rows
     *
     * @param string $key        A key
     * @param mixed  $identifier An identifier
     *
     * @return int
     */
    protected function removeItem($key, $identifier)
    {
        $this->mysqli->query(
            sprintf(
                "DELETE FROM `%s` WHERE `%s` = '%s'",
                $this->tableName,
                $key,
                $this->escape($identifier)
            )
        );

        return $this->mysqli->affected_rows;
    }

    /**
     * Returns an item
     * If item does not exists, return null
     *
     * @param string $key        A key
     * @param mixed  $identifier An identifier
     *
     * @return array|null
     */
    protected function getItem($key, $identifier)
    {
        $result = $this->mysqli->query(
            sprintf(
                "SELECT * FROM `%s` WHERE `%s` = '%s'",
                $this->tableName,
                $key,
                $this->escape($identifier)
            )
        );
        $item   = $result->fetch_assoc();
        $result->free();

        return is_array($item) ? $item : null;
    }

}
