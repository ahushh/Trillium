<?php

/**
 * Part of the Trillium
 *
 * @package Trillium
 */

namespace Trillium\ImageBoard\Service\Post;

use Trillium\Exception\UnexpectedValueException;

/**
 * Post Class
 *
 * @package Trillium\ImageBoard\Service\Post
 */
class Post
{
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
    public function __construct(Model $model)
    {
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
    public function create(array $data)
    {
        return $this->model->create($data);
    }

    /**
     * Get list of the posts
     *
     * @param int $id ID of the thread
     *
     * @return array
     */
    public function getList($id)
    {
        return $this->model->getPosts($id);
    }

    /**
     * Remove post(s)
     *
     * @param array|string|int $id ID(s)
     * @param string           $by Remove by
     *
     * @throws UnexpectedValueException
     * @return void
     */
    public function remove($id, $by)
    {
        $expected = [self::ID, self::BOARD, self::THREAD];
        if (!in_array($by, $expected)) {
            throw new UnexpectedValueException('by', implode(', ', $expected));
        }
        $this->model->remove($by, $id);
    }

    /**
     * Find post by ID
     *
     * @param int $id ID of the post
     *
     * @return array|null
     */
    public function get($id)
    {
        return $this->model->get($id);
    }

    /**
     * Get time of last post for given IP
     *
     * @param int $ip IP address in the long format
     *
     * @return int|null
     */
    public function timeOfLastIP($ip)
    {
        return $this->model->timeOfLastIP($ip);
    }

    /**
     * Update data of the post
     *
     * @param array  $data  Data of the post
     * @param string $key   Update by
     * @param mixed  $value Value of the field
     *
     * @return void
     */
    public function update(array $data, $key, $value)
    {
        $this->model->update($data, $key, $value);
    }

}
