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
     * @param int      $tid ID of the thread
     * @param int|null $pid ID of the first post
     *
     * @return void
     */
    public function bump($tid, $pid = null) {
        $this->model->bump($tid, $pid);
    }

}