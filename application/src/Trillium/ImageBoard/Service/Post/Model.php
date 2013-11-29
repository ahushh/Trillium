<?php

/**
 * Part of the Trillium
 *
 * @package Trillium
 */

namespace Trillium\ImageBoard\Service\Post;

use Trillium\Model\ModelExtended;

/**
 * Model Class
 *
 * @package Trillium\ImageBoard\Service\Post
 */
class Model extends ModelExtended {

    /**
     * Create the post
     *
     * @param array $data Data of the post
     *
     * @return int
     */
    public function create(array $data) {
        return $this->save($data);
    }

    /**
     * Get list of the posts
     *
     * @param int $id ID of the thread
     *
     * @throws \InvalidArgumentException
     * @return array
     */
    public function getPosts($id) {
        if (!is_int($id)) {
            throw new \InvalidArgumentException('Unexpected type of the ID. Integer expected.');
        }
        return $this->getList(['by' => 'id', 'direction' => 'ASC'], "`thread` = '" . $id . "'");
    }

    /**
     * Remove post(s)
     *
     * @param string           $key   Remove by
     * @param array|int|string $value ID(s)
     *
     * @return void
     */
    public function remove($key, $value) {
        parent::remove($key, $value);
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
        return $this->findItem('id', $id);
    }

} 