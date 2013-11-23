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

    /**
     * Remove post(s)
     *
     * @param array|string|int $id ID(s)
     * @param string           $by Remove by
     *
     * @return void
     * @throws \UnexpectedValueException
     */
    public function remove($id, $by) {
        $by = in_array($by, ['id', 'board', 'thread']) ? $by : null;
        if ($by === null) {
            throw new \UnexpectedValueException('Unexpected value of the $by: id, board or thread expected');
        }
        if (is_array($id)) {
            $id = array_map(function ($id) {
                return is_string($id) ? $this->db->real_escape_string($id) : $id;
            }, $id);
            $id = "IN ('" . implode("', '", $id) . "')";
        } else {
            $id = "= '" .  (is_string($id) ? $this->db->real_escape_string($id) : $id) . "'";
        }
        $this->db->query("DELETE FROM `posts` WHERE `" . $by . "` " . $id);
    }

    /**
     * Find post by ID
     * Returns null if post is not exists
     *
     * @param int $id ID
     * @return array|null
     * @throws \InvalidArgumentException
     */
    public function get($id) {
        if (!is_int($id)) {
            throw new \InvalidArgumentException('Unexpected type of the $id, integer expected');
        }
        $result = $this->db->query("SELECT * FROM `posts` WHERE `id` = '" . $id . "'");
        $data = $result->fetch_assoc();
        $result->free();
        return is_array($data) ? $data : null;
    }

}