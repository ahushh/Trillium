<?php

/**
 * Part of the Trillium
 *
 * @package Trillium
 */

namespace Trillium\ImageBoard\Service;

use Trillium\ImageBoard\Model\Board as Model;

/**
 * Board Class
 *
 * @package Trillium\ImageBoard\Service
 */
class Board {

    /**
     * @var \Trillium\ImageBoard\Model\Board Model
     */
    private $model;

    /**
     * @var array Stored data
     */
    private $stored;

    /**
     * @param Model $model Model
     */
    public function __construct(Model $model) {
        $this->model = $model;
        $this->stored = [];
    }

    /**
     * Get data of the board
     * Returns null, if board is not exists
     *
     * @param string $name Name of the board
     *
     * @return array|null
     */
    public function get($name) {
        return $this->model->get($name);
    }

    /**
     * Get list of the boards
     *
     * @param boolean $includeHidden Include hidden boards
     *
     * @return array
     */
    public function getList($includeHidden = true) {
        if (!array_key_exists('list', $this->stored)) {
            $this->stored['list']['all'] = $this->model->getList();
        }
        if ($includeHidden === false && !isset($this->stored['list']['non_hidden'])) {
            $this->stored['list']['non_hidden'] = [];
            foreach ($this->stored['list']['all'] as $board) {
                if ($board['hidden']) {
                    continue;
                }
                $this->stored['list']['non_hidden'][] = $board;
            }
        }
        return $includeHidden ? $this->stored['list']['all'] : $this->stored['list']['non_hidden'];
    }

    /**
     * Check board for exists
     *
     * @param string $name Name of the board
     *
     * @return boolean
     */
    public function isExists($name) {
        return $this->model->isExists($name);
    }

    /**
     * Save board
     *
     * @param array $data Data
     *
     * @return void
     */
    public function save(array $data) {
        $this->model->save($data);
    }

    /**
     * Remove board
     *
     * @param string $name Name of the board
     *
     * @return void
     */
    public function remove($name) {
        $this->model->remove($name);
    }

}