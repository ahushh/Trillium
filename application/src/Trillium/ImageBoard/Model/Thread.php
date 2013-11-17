<?php

/**
 * Part of the Trillium
 *
 * @package Trillium
 */

namespace Trillium\ImageBoard\Model;

use Trillium\Model\Model;

/**
 * Thread Class
 *
 * @package Trillium\ImageBoard\Model
 */
class Thread extends Model {

    /**
     * Create the thread
     *
     * @param string $board Name of the board
     * @param string $theme Theme iof the thread
     *
     * @return int
     */
    public function create($board, $theme) {
        $board = $this->db->real_escape_string($board);
        $theme = $this->db->real_escape_string($theme);
        $this->db->query("INSERT INTO `threads` SET `board` = '" . $board . "', `theme` = '" . $theme . "', `created` = '" . time() . "'");
        return (int) $this->db->insert_id;
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
            $this->db->query(
                "UPDATE `threads` SET "
                . implode(",", $statement)
                . "WHERE `id` = '" . $tid . "'"
            );
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
            $board = $this->db->real_escape_string($board);
            $where = "WHERE `threads`.`board` = '" . $board . "'";
        } else {
            $where = "";
        }
        if ($offset !== null || $limit !== null) {
            $limit = "LIMIT " . (int) $offset . ", " . (int) $limit;
        } else {
            $limit = "";
        }
        $result = $this->db->query(
            "SELECT `threads`.*, `posts`.`text` FROM `threads` "
            . "LEFT JOIN `posts` ON `threads`.`op` = `posts`.`id` "
            . $where
            . " ORDER BY `bump` DESC "
            . $limit
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
        $result = $this->db->query("SELECT * FROM `threads` WHERE `id` = '" . $id . "'");
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
        $board = $this->db->real_escape_string($board);
        $result = $this->db->query("SELECT COUNT(*) FROM `threads` WHERE `board` = '" . $board . "'");
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
        $board = $this->db->real_escape_string($board);
        $redundant = (int) $redundant;
        $result = $this->db->query("SELECT * FROM `threads` WHERE `board` = '" . $board . "' ORDER BY `bump` ASC LIMIT 0, " . $redundant);
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
     * @return void
     * @throws \UnexpectedValueException
     */
    public function remove($id, $by) {
        $by = in_array($by, ['id', 'board']) ? $by : null;
        if ($by === null) {
            throw new \UnexpectedValueException('Unexpected value of the $by: id or board expected');
        }
        if (is_array($id)) {
            $id = array_map(function ($id) {
                return is_string($id) ? $this->db->real_escape_string($id) : $id;
            }, $id);
            $id = "IN ('" . implode("', '", $id) . "')";
        } else {
            $id = "= '" .  (is_string($id) ? $this->db->real_escape_string($id) : $id) . "'";
        }
        $this->db->query("DELETE FROM `threads` WHERE `" . $by . "` " . $id);
    }

}