<?php

/**
 * Part of the Trillium
 *
 * @package Trillium
 */

namespace Trillium\ImageBoard\Service\Board;

use Trillium\Model\ModelExtended;

/**
 * Model Class
 *
 * @package Trillium\ImageBoard\Service\Board
 */
class Model extends ModelExtended
{
    /**
     * Get data of the board
     * Returns null, if board is not exists
     *
     * @param string $name Name of the board
     *
     * @return array|null
     */
    public function get($name)
    {
        return $this->findItem('name', $name);
    }

    /**
     * Get list of the boards
     *
     * @return array
     */
    public function getBoards()
    {
        return parent::getList(['by' => 'name', 'direction' => 'ASC']);
    }

    /**
     * Check board for exists
     *
     * @param string $name Name of the board
     *
     * @return boolean
     */
    public function isExists($name)
    {
        $name = $this->db->real_escape_string($name);
        $result = $this->db->query("SELECT COUNT(*) FROM `" . $this->tableName . "` WHERE `name` = '" . $name . "'");
        $isExists = (bool) $result->fetch_row()[0];
        $result->free();

        return $isExists;
    }

    /**
     * Save board
     *
     * @param array $data Data
     *
     * @return void
     */
    public function saveBoard(array $data)
    {
        parent::save($data, true);
    }

    /**
     * Remove board
     *
     * @param string $name Name of the board
     *
     * @return void
     */
    public function removeBoard($name)
    {
        parent::remove('name', $name);
    }

}
