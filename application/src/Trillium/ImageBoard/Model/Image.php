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

}