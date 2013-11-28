<?php

/**
 * Part of the Trillium
 *
 * @package Trillium
 */

namespace Trillium\ImageBoard\Service;

/**
 * Thread Class
 *
 * @package Trillium\ImageBoard\Service
 */
class Thread {

    /**
     * string Name of the table in database
     */
    const TABLE_NAME = 'threads';

    /**
     * @var \mysqli MySQLi object
     */
    private $mysqli;

    /**
     * @param \mysqli $mysqli MySQLi object
     */
    public function __construct(\mysqli $mysqli) {
        $this->mysqli = $mysqli;
    }

    /**
     * Create the thread
     *
     * @param string $board Name of the board
     * @param string $theme Theme of the thread
     *
     * @return int
     */
    public function create($board, $theme) {
        $this->mysqli->query(
            "INSERT INTO `" . self::TABLE_NAME . "` SET "
            . "`board` = '" . $this->mysqli->real_escape_string($board) . "', "
            . "`theme` = '" . $this->mysqli->real_escape_string($theme) . "', "
            . "`created` = '" . time() . "'"
        );
        return (int) $this->mysqli->insert_id;
    }

    /**
     * Bump thread
     *
     * @param int      $tid  ID of the thread
     * @param int|null $pid  ID of the first post
     * @param boolean  $bump Update time?
     *
     * @throws \InvalidArgumentException
     * @return void
     */
    public function bump($tid, $pid = null, $bump = true) {
        if (!is_int($tid)) {
            throw new \InvalidArgumentException('Unexpected type of tid. Integer expected.');
        }
        $statement = [];
        if ($bump) {
            $statement[] = "`bump` = '" . time() . "'";
        }
        if ($pid !== null) {
            $statement[] = "`op` = '" . (int) $pid . "'";
        }
        if (!empty($statement)) {
            $this->mysqli->query("UPDATE `" . self::TABLE_NAME . "` SET " . implode(",", $statement) . " WHERE `id` = '" . $tid . "'");
        }
    }

    /**
     * Get list of the threads
     *
     * @param string|null $board  Name of the board
     * @param int|null    $offset Offset
     * @param int|null    $limit  Limit
     *
     * @return array
     */
    public function getList($board = null, $offset = null, $limit = null) {
        if ($board !== null) {
            $board = $this->mysqli->real_escape_string($board);
            $where = "WHERE `" . self::TABLE_NAME . "`.`board` = '" . $board . "'";
        }
        $result = $this->mysqli->query(
            "SELECT `" . self::TABLE_NAME . "`.*, `" . Post::TABLE_NAME . "`.`text` FROM `" . self::TABLE_NAME . "` "
            . "LEFT JOIN `" . Post::TABLE_NAME . "` ON `" . self::TABLE_NAME . "`.`op` = `" . Post::TABLE_NAME . "`.`id` "
            . (isset($where) ? $where : "")
            . " ORDER BY `bump` DESC "
            . ($offset !== null || $limit !== null ? "LIMIT " . (int) $offset . ", " . (int) $limit : "")
        );
        $list = [];
        while (($item = $result->fetch_assoc())) {
            $list[] = $item;
        }
        $result->free();
        return $list;
    }

    /**
     * Get the thread
     *
     * @param int $id ID
     *
     * @throws \InvalidArgumentException
     * @return array|null
     */
    public function get($id) {
        if (!is_int($id)) {
            throw new \InvalidArgumentException('Unexpected type of ID. Integer expected.');
        }
        $result = $this->mysqli->query("SELECT * FROM `" . self::TABLE_NAME . "` WHERE `id` = '" . $id . "'");
        $data = $result->fetch_assoc();
        $result->free();
        return is_array($data) ? $data : null;
    }

    /**
     * Get number of threads in the board
     *
     * @param string $board Name of the board
     *
     * @return int
     */
    public function total($board) {
        $board = $this->mysqli->real_escape_string($board);
        $result = $this->mysqli->query("SELECT COUNT(*) FROM `" . self::TABLE_NAME . "` WHERE `board` = '" . $board . "'");
        $total = (int) $result->fetch_row()[0];
        $result->free();
        return $total;
    }

    /**
     * Get IDs of redundant threads
     *
     * @param string $board    Name of the board
     * @param int    $redundant Redudant
     *
     * @return array
     */
    public function getRedundant($board, $redundant) {
        $result = $this->mysqli->query(
            "SELECT * FROM `" . self::TABLE_NAME . "` "
            . "WHERE `board` = '" . $this->mysqli->real_escape_string($board) . "' "
            . "ORDER BY `bump` ASC LIMIT 0, " . (int) $redundant
        );
        $list = [];
        while (($item = $result->fetch_assoc())) {
            $list[] = (int) $item['id'];
        }
        $result->free();
        return $list;
    }

    /**
     * Remove thread(s)
     *
     * @param string|int|array $id ID(s)
     * @param string           $by Remove by
     *
     * @throws \UnexpectedValueException
     * @return void
     */
    public function remove($id, $by) {
        if ($by !== 'id' && $by !== 'board') {
            throw new \UnexpectedValueException('Unexpected value of the $by: id or board expected');
        }
        if (is_array($id)) {
            $id = array_map(
                function ($id) {
                    return is_string($id) ? $this->mysqli->real_escape_string($id) : (int) $id;
                },
                $id
            );
            $id = "IN ('" . implode("', '", $id) . "')";
        } else {
            $id = "= '" .  (is_string($id) ? $this->mysqli->real_escape_string($id) : (int) $id) . "'";
        }
        $this->mysqli->query("DELETE FROM `" . self::TABLE_NAME . "` WHERE `" . $by . "` " . $id);
    }

}