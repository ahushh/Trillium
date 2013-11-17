<?php

/**
 * Part of the Trillium
 *
 * @package Trillium
 */

namespace Trillium\ImageBoard\Model;

use Trillium\Model\Model;

/**
 * Post Class
 *
 * @package Trillium\ImageBoard\Model
 */
class Post extends Model {

    /**
     * Create the post
     * Returns ID of the post
     *
     * @param string  $board     Name of the board
     * @param int     $thread    ID of the thread
     * @param string  $text      Text of the post
     * @param boolean $sage      Sage
     * @param int     $ip        IP Address of the author
     * @param string  $userAgent User-Agent of the author
     *
     * @throws \InvalidArgumentException
     * @return int
     */
    public function create($board, $thread, $text, $sage, $ip, $userAgent) {
        if (!is_int($thread)) {
            throw new \InvalidArgumentException('Unexpected type of the thread. Integer expected');
        }
        if (!is_int($ip)) {
            throw new \InvalidArgumentException('Unexpected type of the ip. Integer expected');
        }
        $board = $this->db->real_escape_string($board);
        $text = $this->db->real_escape_string($text);
        $userAgent = $this->db->real_escape_string($userAgent);
        $sage = $sage ? 1 : 0;
        $this->db->query(
            "INSERT INTO `posts` SET "
            . "`board` = '" . $board . "',"
            . "`thread` = '" . $thread . "',"
            . "`text` = '" . $text . "',"
            . "`sage` = '" . $sage . "',"
            . "`ip` = '" . $ip . "',"
            . "`user_agent` = '" . $userAgent . "',"
            . "`time` = '" . time() . "'"
        );
        return (int) $this->db->insert_id;
    }

    /**
     * Get list of the posts
     *
     * @param int $id ID of the thread
     *
     * @throws \InvalidArgumentException
     * @return array
     */
    public function getList($id) {
        if (!is_int($id)) {
            throw new \InvalidArgumentException('Unexpected type of the ID. Integer expected.');
        }
        $list = [];
        $result = $this->db->query("SELECT * FROM `posts` WHERE `thread` = '" . $id . "' ORDER BY `id` ASC");
        while (($item = $result->fetch_assoc())) {
            $list[] = $item;
        }
        $result->free();
        return $list;
    }

} 