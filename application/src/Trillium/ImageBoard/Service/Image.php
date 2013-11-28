<?php

/**
 * Part of the Trillium
 *
 * @package Trillium
 */

namespace Trillium\ImageBoard\Service;

/**
 * Image Class
 *
 * @package Trillium\ImageBoard\Service
 */
class Image {

    /**
     * string Name of the table in database
     */
    const TABLE_NAME = 'images';

    /**
     * @var \mysqli $mysqli MySQLi object
     */
    private $mysqli;

    /**
     * Create instance
     *
     * @param \mysqli $mysqli MySQLi object
     *
     * @return Image
     */
    public function __construct(\mysqli $mysqli) {
        $this->mysqli = $mysqli;
    }

    /**
     * Insert data of the images
     *
     * @param array $images
     *
     * @return void
     */
    public function insert(array $images) {
        $statement = "INSERT INTO `" . self::TABLE_NAME . "` (`board`, `thread`, `post`, `name`, `ext`, `width`, `height`, `size`) VALUES ";
        foreach ($images as $image) {
            $statement .= "("
                . "'" . $this->mysqli->real_escape_string($image['board']) . "', "
                . "'" . (int) $image['thread'] . "', "
                . "'" . (int) $image['post'] . "', "
                . "'" . $this->mysqli->real_escape_string($image['name']) . "',"
                . "'" . $this->mysqli->real_escape_string($image['ext']) . "', "
                . "'" . (int) $image['width'] . "', "
                . "'" . (int) $image['height'] . "', "
                . "'" . (int) $image['size'] . "'"
                . "),";
        }
        $this->mysqli->query(rtrim($statement, ","));
    }

    /**
     * Returns list of the images
     *
     * @param array|int $id ID
     * @param string    $by Key @todo use const
     *
     * @throws \UnexpectedValueException
     * @throws \InvalidArgumentException
     * @return array
     */
    public function getList($id, $by = 'thread') {
        if (!is_int($id) && !is_array($id)) {
            throw new \InvalidArgumentException('Unexpected type of the $thread. Integer or array expected');
        }
        if (!in_array($by, ['board', 'thread', 'post'])) {
            throw new \UnexpectedValueException('Unexpected value of the argument $by. board, thread or post expected');
        }
        if (is_array($id)) {
            $id = array_map('intval', $id);
            $id = "IN('" . implode("', '", $id) . "')";
        } else {
            $id = " = '" . $id . "'";
        }
        $list = [];
        $result = $this->mysqli->query("SELECT * FROM `" . self::TABLE_NAME . "` WHERE `" . $by . "` " . $id);
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
     * @throws \UnexpectedValueException
     * @return void
     */
    public function remove($id, $by) {
        if (!in_array($by, ['id', 'board', 'thread', 'post'])) {
            throw new \UnexpectedValueException('Unexpected value of the $by: id, board, thread or post expected');
        }
        if (is_array($id)) {
            $id = array_map(
                function ($id) {
                    return is_string($id) ? $this->mysqli->real_escape_string($id) : $id;
                },
                $id
            );
            $id = "IN ('" . implode("', '", $id) . "')";
        } else {
            $id = "= '" .  (is_string($id) ? $this->mysqli->real_escape_string($id) : $id) . "'";
        }
        $this->mysqli->query("DELETE FROM `" . self::TABLE_NAME . "` WHERE `" . $by . "` " . $id);
    }

    /**
     * Get data of the image
     * Returns null, if image is not exists
     *
     * @param int $id ID of the image
     *
     * @throws \InvalidArgumentException
     * @return array|null
     */
    public function get($id) {
        if (!is_int($id)) {
            throw new \InvalidArgumentException('Unexpected type of the $id argument. Integer expected.');
        }
        $result = $this->mysqli->query("SELECT * FROM `" . self::TABLE_NAME . "` WHERE `id` = '" . $id . "'");
        $data = $result->fetch_assoc();
        $result->free();
        return is_array($data) ? $data : null;
    }

} 