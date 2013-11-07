<?php

/**
 * Part of the Trillium
 *
 * @package Trillium
 */

namespace Application\Model;


use Trillium\Model\Model;

/**
 * Boards Class
 *
 * Boards Model
 *
 * @package Application\Model
 */
class Boards extends Model {

    /**
     * @var array Stored data
     */
    private $stored = [];

    /**
     * Get data of the board
     * Returns null, if board is not exists
     *
     * @param string $name Name of the board
     *
     * @return array|null
     */
    public function get($name) {
        $name = $this->db->real_escape_string($name);
        $result = $this->db->query("SELECT * FROM `boards` WHERE `name` = '$name'");
        $data = $result->fetch_assoc();
        $result->free();
        return is_array($data) ? $data : null;
    }

    /**
     * Check board name for exists
     *
     * @param string $name Name of the board
     *
     * @return boolean
     */
    public function isExists($name) {
        $name = $this->db->real_escape_string($name);
        $result = $this->db->query("SELECT COUNT(*) FROM `boards` WHERE `name` = '$name'");
        $isExists = (bool) $result->fetch_row()[0];
        $result->free();
        return $isExists;
    }

    /**
     * Get list of the boards
     *
     * @return array
     */
    public function getList() {
        if (!isset($this->stored['getList'])) {
            $result = $this->db->query("SELECT * FROM `boards` ORDER BY `name` ASC");
            $this->stored['getList'] = [];
            while (($item = $result->fetch_assoc())) {
                $this->stored['getList'][] = $item;
            }
            $result->free();
        }
        return $this->stored['getList'];
    }

    /**
     * Save board
     *
     * @param array  $data Data of the board
     *
     * @throws \RuntimeException
     * @return void
     */
    public function save(array $data) {
        if (empty($data)) {
            throw new \RuntimeException('Data is empty.');
        }
        $statement = "";
        foreach ($data as $key => $value) {
            $value = is_numeric($value)
                ? (int) $value
                : (is_string($value)
                    ? $this->db->real_escape_string($value)
                    : (is_int($value)
                        ? $value
                        : null
                    )
                );
            if ($value === null) {
                throw new \RuntimeException('Unexpected type of the value. String, int or array expected');
            }
            $statement .= "`" . $key . "` = '" .$value . "',";
        }
        $statement = rtrim($statement, ",");
        $this->db->query("INSERT INTO `boards` SET " . $statement . " ON DUPLICATE KEY UPDATE " . $statement);
    }

    /**
     * Remove board
     *
     * @param string $name Name of the board
     *
     * @return void
     */
    public function remove($name) {
        $name = $this->db->real_escape_string($name);
        $this->db->query("DELETE FROM `boards` WHERE `name` = '" . $name . "'");
    }

}