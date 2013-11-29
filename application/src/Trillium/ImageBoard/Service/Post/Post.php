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
     * Name of the ID key
     */
    const ID = 'id';

    /**
     * Name of the board key
     */
    const BOARD = 'board';

    /**
     * Name of the thread key
     */
    const THREAD = 'thread';

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
        if (!in_array($by, self::ID, self::BOARD, self::THREAD)) {
            throw new \UnexpectedValueException('Unexpected value of argument $by');
        }
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