<?php

/**
 * Part of the Trillium
 *
 * @package Trillium
 */

namespace Trillium\ImageBoard\Service\Thread;

/**
 * Thread Class
 *
 * @package Trillium\ImageBoard\Service\Thread
 */
class Thread {

    /**
     * @var Model Model
     */
    private $model;

    /**
     * Create Thread instance
     *
     * @param Model $model Model
     *
     * @return Thread
     */
    public function __construct(Model $model) {
        $this->model = $model;
    }

    /**
     * Create the thread
     *
     * @param string $board Name of the board
     * @param string $theme Theme of the thread
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
     * @throws \InvalidArgumentException
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
        return $this->model->getThreads($board, $offset, $limit);
    }

    /**
     * Get the thread
     *
     * @param int $id ID
     *
     * @throws \InvalidArgumentException
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
     * @throws \UnexpectedValueException
     * @return void
     */
    public function remove($id, $by) {
        $this->model->remove($by, $id);
    }

} 