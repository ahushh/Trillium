<?php

/**
 * Part of the Trillium
 *
 * @package Trillium
 */

namespace Trillium\ImageBoard\Service;

use Trillium\ImageBoard\Model\Thread as Model;

/**
 * Thread Class
 *
 * @package Trillium\ImageBoard\Service
 */
class Thread {

    /**
     * @var \Trillium\ImageBoard\Model\Thread Model
     */
    private $model;

    /**
     * @param Model $model Model
     */
    public function __construct(Model $model) {
        $this->model = $model;
    }

    /**
     * Create the thread
     *
     * @param string $board Name of the board
     * @param string $theme Theme iof the thread
     *
     * @return int
     */
    public function create($board, $theme) {
        return $this->model->create($board, $theme);
    }

    /**
     * Bump thread
     *
     * @param int      $tid  ID of the thread
     * @param int|null $pid  ID of the first post
     * @param boolean  $bump Update time?
     *
     * @return void
     */
    public function bump($tid, $pid = null, $bump = true) {
        $this->model->bump($tid, $pid, $bump);
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
        return $this->model->getList($board, $offset, $limit);
    }

    /**
     * Get the thread
     *
     * @param int $id ID
     *
     * @return array|null
     */
    public function get($id) {
        return $this->model->get($id);
    }

    /**
     * Get number of threads in the board
     *
     * @param string $board Name of the board
     *
     * @return int
     */
    public function total($board) {
        return $this->model->total($board);
    }

    /**
     * Get IDs of redundant threads
     *
     * @param string $board    Name of the board
     * @param int    $redundant Redudant
     *
     * @return array
     */
    public function getRedundant($board, $redundant) {
        return $this->model->getRedundant($board, $redundant);
    }

    /**
     * Remove thread(s)
     *
     * @param string|int|array $id ID(s)
     * @param string           $by Remove by
     *
     * @return void
     */
    public function remove($id, $by) {
        $this->model->remove($id, $by);
    }

}