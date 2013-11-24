<?php

/**
 * Part of the Trillium
 *
 * @package Trillium
 */

namespace Trillium\ImageBoard\Service;

use Trillium\ImageBoard\Model\Post as Model;

/**
 * Post Class
 *
 * @package Trillium\ImageBoard\Service
 */
class Post {

    /**
     * @var \Trillium\ImageBoard\Model\Post Model
     */
    private $model;

    /**
     * Create Post instance
     *
     * @param Model $model Model
     *
     * @return Post
     */
    public function __construct(Model $model) {
        $this->model = $model;
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
     * @return int
     */
    public function create($board, $thread, $text, $video, $sage, $ip, $userAgent) {
        return $this->model->create($board, $thread, $text, $video, $sage, $ip, $userAgent);
    }

    /**
     * Get list of the posts
     *
     * @param int $id ID of the thread
     *
     * @return array
     */
    public function getList($id) {
        return $this->model->getList($id);
    }

    /**
     * Remove post(s)
     *
     * @param array|string|int $id ID(s)
     * @param string           $by Remove by
     *
     * @return void
     */
    public function remove($id, $by) {
        $this->model->remove($id, $by);
    }

    /**
     * Find post by ID
     *
     * @param int $id ID of the post
     *
     * @return array|null
     */
    public function get($id) {
        return $this->model->get($id);
    }

}