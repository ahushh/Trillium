<?php

/**
 * Part of the Trillium
 *
 * @package Trillium
 */

namespace Trillium\ImageBoard\Service\Image;

use Trillium\Model\ModelExtended;

/**
 * Model Class
 *
 * @package Trillium\ImageBoard\Service\Image
 */
class Model extends ModelExtended {

    /**
     * Insert data of the images
     *
     * @param array $images
     *
     * @return void
     */
    public function insert(array $images) {
        $statement = "INSERT INTO `" . $this->tableName . "` (`board`, `thread`, `post`, `name`, `ext`, `width`, `height`, `size`) VALUES ";
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
        $this->db->query(rtrim($statement, ","));
    }

    /**
     * Returns list of the images
     *
     * @param array|int $id ID
     * @param string    $by Key
     *
     * @throws \UnexpectedValueException
     * @throws \InvalidArgumentException
     * @return array
     */
    public function getImages($id, $by = 'thread') {
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
        return $this->getList([], "`" . $by . "` " . $id);
    }

    /**
     * Remove image(s)
     *
     * @param string           $key   Remove by
     * @param array|int|string $value ID(s)
     *
     * @throws \UnexpectedValueException
     * @return void
     */
    public function remove($key, $value) {
        if (!in_array($key, ['id', 'board', 'thread', 'post'])) {
            throw new \UnexpectedValueException('Unexpected value of the $by: id, board, thread or post expected');
        }
        parent::remove($key, $value);
    }

    /**
     * Get data of the image
     * Returns null, if image is not exists
     *
     * @param int $id ID of the image
     *
     * @return array|null
     */
    public function get($id) {
        return $this->findItem('id', $id);
    }

} 