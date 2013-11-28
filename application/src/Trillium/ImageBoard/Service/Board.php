<?php

/**
 * Part of the Trillium
 *
 * @package Trillium
 */

namespace Trillium\ImageBoard\Service;

/**
 * Board Class
 *
 * @package Trillium\ImageBoard\Service
 */
class Board {

    /**
     * string Name of the table in database
     */
    const TABLE_NAME = 'boards';

    /**
     * @var \mysqli $mysqli MySQLi object
     */
    private $mysqli;

    /**
     * @var array Stored data
     */
    private $stored;

    /**
     * Create Board instance
     *
     * @param \mysqli $mysqli MySQLi object
     *
     * @return Board
     */
    public function __construct(\mysqli $mysqli) {
        $this->mysqli = $mysqli;
        $this->stored = [];
    }

    /**
     * Get data of the board
     * Returns null, if board is not exists
     *
     * @param string $name Name of the board
     *
     * @return array|null
     */
    public function get($name) {
        $name = $this->mysqli->real_escape_string($name);
        $result = $this->mysqli->query("SELECT * FROM `" . self::TABLE_NAME . "` WHERE `name` = '" . $name . "'");
        $data = $result->fetch_assoc();
        $result->free();
        return is_array($data) ? $data : null;
    }

    /**
     * Get list of the boards
     *
     * @param boolean $includeHidden Include hidden boards
     *
     * @return array
     */
    public function getList($includeHidden = true) {
        if (!array_key_exists('list', $this->stored)) {
            $this->stored['list']['all'] = [];
            $result = $this->mysqli->query("SELECT * FROM `" . self::TABLE_NAME . "` ORDER BY `name` ASC");
            while (($item = $result->fetch_assoc())) {
                $this->stored['list']['all'][] = $item;
            }
            $result->free();
        }
        if ($includeHidden === false && !isset($this->stored['list']['non_hidden'])) {
            $this->stored['list']['non_hidden'] = [];
            foreach ($this->stored['list']['all'] as $board) {
                if ($board['hidden']) {
                    continue;
                }
                $this->stored['list']['non_hidden'][] = $board;
            }
        }
        return $includeHidden ? $this->stored['list']['all'] : $this->stored['list']['non_hidden'];
    }

    /**
     * Check board for exists
     *
     * @param string $name Name of the board
     *
     * @return boolean
     */
    public function isExists($name) {
        $name = $this->mysqli->real_escape_string($name);
        $result = $this->mysqli->query("SELECT COUNT(*) FROM `" . self::TABLE_NAME . "` WHERE `name` = '" . $name . "'");
        $isExists = (bool) $result->fetch_row()[0];
        $result->free();
        return $isExists;
    }

    /**
     * Save data of the board
     *
     * @param array $data Data
     *
     * @throws \LogicException
     * @throws \RuntimeException
     * @return void
     */
    public function save(array $data) {
        if (empty($data)) {
            throw new \LogicException('Data is empty');
        }
        $statement = "";
        foreach ($data as $key => $value) {
            if (is_numeric($value)) {
                $value = (int) $value;
            } elseif (is_string($value)) {
                $value = $this->mysqli->real_escape_string($value);
            } elseif (!is_int($value)) {
                throw new \RuntimeException('Unexpected type of the value. String, integer or array expected');
            }
            $statement .= "`" . $key . "` = '" .$value . "',";
        }
        $statement = rtrim($statement, ",");
        $this->mysqli->query("INSERT INTO `" . self::TABLE_NAME . "` SET " . $statement . " ON DUPLICATE KEY UPDATE " . $statement);
    }

    /**
     * Remove board
     *
     * @param string $name Name of the board
     *
     * @return void
     */
    public function remove($name) {
        $name = $this->mysqli->real_escape_string($name);
        $this->mysqli->query("DELETE FROM `" . self::TABLE_NAME . "` WHERE `name` = '" . $name . "'");
    }

}