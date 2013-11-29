<?php

/**
 * Part of the Trillium
 *
 * @package Trillium
 */

namespace Trillium\Model;

/**
 * ModelExtended Class
 *
 * Extended Model
 *
 * @package Trillium\Model
 */
class ModelExtended extends Model {

    /**
     * @var string Name of the table in database
     */
    protected $tableName;

    /**
     * Create ModelExtended instance
     *
     * @param MySQLi $db        MySQLi Object
     * @param string $tableName Name of the table in database
     *
     * @return ModelExtended
     */
    public function __construct(MySQLi $db, $tableName) {
        parent::__construct($db);
        $this->tableName = $tableName;
    }

    /**
     * Find item
     * Returns null, if item is not found
     *
     * @param string $key   Name of the field
     * @param string $value Value
     *
     * @throws \InvalidArgumentException
     * @return array|null
     */
    protected function findItem($key, $value) {
        $value = is_string($value) ? $this->db->real_escape_string($value) : (is_int($value) ? $value : null);
        if ($value === null) {
            throw new \InvalidArgumentException('Unexpected type of the value. Int or string expected');
        }
        $result = $this->db->query("SELECT * FROM `" . $this->tableName . "` WHERE `" . $key . "` = '" . $value . "'");
        $data = $result->fetch_assoc();
        $result->free();
        return is_array($data) ? $data : null;
    }

    /**
     * Get list of items
     *
     * @param array  $order Order [by => string, direction => string]
     * @param string $where Where
     * @param array  $limit Limit [offset => int, limit => int]
     *
     * @return array
     */
    protected function getList(array $order = [], $where = '', $limit = []) {
        $statement = "SELECT * FROM `" . $this->tableName . "`";
        if (!empty($where)) {
            $statement .= "WHERE " . $where;
        }
        if (isset($order['by']) && isset($order['direction'])) {
            $statement .= " ORDER BY `" . $order['by'] . "` " . $order['direction'];
        }
        if (isset($limit['offset']) && isset($limit['limit'])) {
            $statement .= " LIMIT " . (int) $limit['offset'] . ", " . (int) $limit['limit'];
        }
        $list = [];
        $result = $this->db->query($statement);
        while (($item = $result->fetch_assoc())) {
            $list[] = $item;
        }
        $result->free();
        return $list;
    }

    /**
     * Save data
     *
     * @param array   $data          Data
     * @param boolean $updateExists  Update if exists
     *
     * @throws \LogicException Empty Data or unexpected type of item
     * @return mixed
     */
    protected function save(array $data, $updateExists = false) {
        if (empty($data)) {
            throw new \LogicException('Empty data');
        }
        $statement = "";
        foreach ($data as $key => $value) {
            if (is_string($value)) {
                $value = $this->db->real_escape_string($value);
            } elseif (!is_int($value)) {
                throw new \LogicException('Unexpected type of the value. String, integer or array expected');
            }
            $statement .= "`" . $key . "` = '" .$value . "',";
        }
        $statement = rtrim($statement, ",");
        if ($updateExists) {
            $statement .= " ON DUPLICATE KEY UPDATE " . $statement;
        }
        $this->db->query("INSERT INTO `" . $this->tableName . "` SET " . $statement);
        return !$updateExists ? $this->db->insert_id : $this;
    }

    /**
     * Remove item
     *
     * @param string           $key   Key
     * @param array|int|string $value Value
     *
     * @return int
     * @throws \InvalidArgumentException
     */
    protected function remove($key, $value) {
        if (is_array($value)) {
            $value = array_map(
                function ($id) {
                    return is_string($id) ? $this->db->real_escape_string($id) : (int) $id;
                },
                $value
            );
            $value = "IN ('" . implode("', '", $value) . "')";
        } elseif (is_string($value) || is_int($value)) {
            $value = "= '" .  (is_string($value) ? $this->db->real_escape_string($value) : (int) $value) . "'";
        } else {
            throw new \InvalidArgumentException('Unexpected type of the value. Array, string or integer expected.');
        }
        $this->db->query("DELETE FROM `" . $this->tableName . "` WHERE `" . $key . "` " . $value);
        return $this->db->affected_rows;
    }

    /**
     * Count
     *
     * @param string $where Where
     *
     * @return int
     */
    public function count($where = '') {
        $where = !empty($where) ? "WHERE " . $where : '';
        $result = $this->db->query("SELECT COUNT(*) FROM `" . $this->tableName . "`" . $where);
        $count = (int) $result->fetch_row()[0];
        $result->free();
        return $count;
    }

}