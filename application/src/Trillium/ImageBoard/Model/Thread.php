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
     * @param int      $tid ID of the thread
     * @param int|null $pid ID of the first post
     *
     * @return void
     * @throws \InvalidArgumentException
     */
    public function bump($tid, $pid = null) {
        if (!is_int($tid)) {
            throw new \InvalidArgumentException('Unexpected type of tid. Integer expected.');
        }
        $this->db->query(
            "UPDATE `threads` SET "
            . "`bump` = '" . time() . "' "
            . ($pid !== null ? ",`op` = '" . (int) $pid . "' " : "")
            . "WHERE `id` = '" . $tid . "'"
        );
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

}