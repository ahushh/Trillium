<?php

/**
 * Part of the Trillium
 *
 * @package Trillium
 */

namespace Trillium\ImageBoard\Service;

/**
 * Post Class
 *
 * @package Trillium\ImageBoard\Service
 */
class Post {

    const TABLE_NAME = 'posts';

    /**
     * @var \mysqli $mysqli MySQLi object
     */
    private $mysqli;

    /**
     * Create Post instance
     *
     * @param \mysqli $mysqli MySQLi object
     *
     * @return Post
     */
    public function __construct(\mysqli $mysqli) {
        $this->mysqli = $mysqli;
    }

    /**
     * Create the post
     * Returns ID of the post
     *
     * @param string  $board     Name of the board
     * @param int     $thread    ID of the thread
     * @param string  $text      Text of the post
     * @param string  $video     Video URL
     * @param boolean $sage      Sage
     * @param int     $ip        IP Address of the author
     * @param string  $userAgent User-Agent of the author
     *
     * @throws \InvalidArgumentException
     * @return int
     */
    public function create($board, $thread, $text, $video, $sage, $ip, $userAgent) {
        if (!is_int($thread)) {
            throw new \InvalidArgumentException('Unexpected type of the thread. Integer expected');
        }
        if (!is_int($ip)) {
            throw new \InvalidArgumentException('Unexpected type of the ip. Integer expected');
        }
        $this->mysqli->query(
            "INSERT INTO `" . self::TABLE_NAME . "` SET "
            . "`board` = '" . $this->mysqli->real_escape_string($board) . "',"
            . "`thread` = '" . $thread . "',"
            . "`text` = '" . $this->mysqli->real_escape_string($text) . "',"
            . "`video` = '" . $this->mysqli->real_escape_string($video) . "',"
            . "`sage` = '" . ($sage ? 1 : 0) . "',"
            . "`ip` = '" . $ip . "',"
            . "`user_agent` = '" . $this->mysqli->real_escape_string($userAgent) . "',"
            . "`time` = '" . time() . "'"
        );
        return (int) $this->mysqli->insert_id;
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
        $result = $this->mysqli->query("SELECT * FROM `" . self::TABLE_NAME . "` WHERE `thread` = '" . $id . "' ORDER BY `id` ASC");
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
     * @throws \UnexpectedValueException
     * @return void
     */
    public function remove($id, $by) {
        if (!in_array($by, ['id', 'board', 'thread'])) {
            throw new \UnexpectedValueException('Unexpected value of the $by: id, board or thread expected');
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

    /**
     * Find post by ID
     *
     * @param int $id ID of the post
     *
     * @throws \InvalidArgumentException
     * @return array|null
     */
    public function get($id) {
        if (!is_int($id)) {
            throw new \InvalidArgumentException('Unexpected type of the $id, integer expected');
        }
        $result = $this->mysqli->query("SELECT * FROM `" . self::TABLE_NAME . "` WHERE `id` = '" . $id . "'");
        $data = $result->fetch_assoc();
        $result->free();
        return is_array($data) ? $data : null;
    }

}