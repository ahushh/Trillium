<?php

/**
 * Part of the Trillium
 *
 * @package Trillium
 */

namespace Trillium\ImageBoard\Model;

use Trillium\Model\Model;

/**
 * Image Class
 *
 * @package Trillium\ImageBoard\Model
 */
class Image extends Model {

    /**
     * Insert data of the images
     *
     * @param array $images
     *
     * @return void
     */
    public function insert(array $images) {
        $statement = "INSERT INTO `images` (`board`, `thread`, `post`, `name`, `ext`, `width`, `height`, `size`) VALUES ";
        foreach ($images as $image) {
            $statement .= "("
                . "'" . $this->db->real_escape_string($image['board']) . "', "
                . "'" . (int) $image['thread'] . "', "
                . "'" . (int) $image['post'] . "', "
                . "'" . $this->db->real_escape_string($image['name']) . "',"
                . "'" . $this->db->real_escape_string($image['ext']) . "', "
                . "'" . (int) $image['width'] . "', "
                . "'" . (int) $image['height'] . "', "
                . "'" . (int) $image['size'] . "'"
            . "),";
        }
        $statement = rtrim($statement, ",");
        $this->db->query($statement);
    }

    /**
     * Returns list of the images for the thread
     *
     * @param array|int $thread ID of the thread
     *
     * @return array
     * @throws \InvalidArgumentException
     */
    public function getList($thread) {
        if (!is_int($thread) && !is_array($thread)) {
            throw new \InvalidArgumentException('Unexpected type of the $thread. Integer or array expected');
        }
        if (is_array($thread)) {
            $thread = array_map('intval', $thread);
            $thread = "IN('" . implode("', '", $thread) . "')";
        } else {
            $thread = " = '" . $thread . "'";
        }
        $list = [];
        $result = $this->db->query("SELECT * FROM `images` WHERE `thread` " . $thread);
        while (($image = $result->fetch_assoc())) {
            $list[(int) $image['post']][] = $image;
        }
        $result->free();
        return $list;
    }

    /**
     * Remove image(s)
     *
     * @param array|string|int $id ID(s)
     * @param string           $by Remove by
     *
     * @return void
     * @throws \UnexpectedValueException
     */
    public function remove($id, $by) {
        $by = in_array($by, ['id', 'board', 'thread', 'post']) ? $by : null;
        if ($by === null) {
            throw new \UnexpectedValueException('Unexpected value of the $by: id, board, thread or post expected');
        }
        if (is_array($id)) {
            $id = array_map(function ($id) {
                return is_string($id) ? $this->db->real_escape_string($id) : $id;
            }, $id);
            $id = "IN ('" . implode("', '", $id) . "')";
        } else {
            $id = "= '" .  (is_string($id) ? $this->db->real_escape_string($id) : $id) . "'";
        }
        $this->db->query("DELETE FROM `images` WHERE `" . $by . "` " . $id);
    }

}