<?php

/**
 * Part of the Trillium
 *
 * @package Trillium
 */

namespace Trillium\ImageBoard\Service\Post;

/**
 * Post Class
 *
 * @package Trillium\ImageBoard\Service\Post
 */
class Post {

    /**
     * @var Model Model
     */
    private $model;

    /**
     * Create Post instance
     *
     * @param Model $model
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
     * @param array $data Data of the post
     *
     * @return int
     */
    public function create(array $data) {
        return $this->model->create($data);
    }

    /**
     * Get list of the posts
     *
     * @param int $id ID of the thread
     *
     * @throws \InvalidArgumentException
     * @return array
     */
    public function getList($id) {
        return $this->model->getPosts($id);
    }

    /**
     * Remove post(s)
     *
     * @param array|string|int $id ID(s)
     * @param string           $by Remove by
     *
     * @throws \UnexpectedValueException
     * @return void
     */
    public function remove($id, $by) {
        $this->model->remove($by, $id);
    }

    /**
     * Find post by ID
     *
     * @param int $id ID of the post
     *
     * @throws \InvalidArgumentException
     * @return array|null
     */
    public function get($id) {
        return $this->model->get($id);
    }

} 