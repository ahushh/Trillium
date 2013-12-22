<?php

/**
 * Part of the Trillium
 *
 * @package Trillium
 */

namespace Trillium\ImageBoard\Service\Post;

use Trillium\Exception\InvalidArgumentException;
use Trillium\Model\ModelExtended;

/**
 * Model Class
 *
 * @package Trillium\ImageBoard\Service\Post
 */
class Model extends ModelExtended
{
    /**
     * Create the post
     *
     * @param array $data Data of the post
     *
     * @return int
     */
    public function create(array $data)
    {
        return $this->save($data);
    }

    /**
     * Get list of the posts
     *
     * @param int $id ID of the thread
     *
     * @throws InvalidArgumentException
     * @return array
     */
    public function getPosts($id)
    {
        if (!is_int($id)) {
            throw new InvalidArgumentException('id', 'integer', gettype($id));
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
    public function remove($key, $value)
    {
        parent::remove($key, $value);
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
        return $this->findItem('id', $id);
    }

    /**
     * Get time of last post for given IP
     *
     * @param int $ip IP address in the long format
     *
     * @throws InvalidArgumentException
     * @return int|null
     */
    public function timeOfLastIP($ip)
    {
        if (!is_int($ip)) {
            throw new InvalidArgumentException('ip', 'integer', gettype($ip));
        }
        $result = $this->db->query("SELECT `time` FROM `" . $this->tableName . "` WHERE `ip` = '" . $ip . "' ORDER BY `time` DESC LIMIT 0,1");
        $time = $result->fetch_row();
        if ($time !== null) {
            $time = (int) $time[0];
        }
        $result->free();

        return $time;
    }

    /**
     * Update data of the post
     *
     * @param array      $data  New Data
     * @param string     $key   Update by
     * @param string|int $value Value of the key
     *
     * @return void
     */
    public function update(array $data, $key, $value)
    {
        parent::update($data, $key, $value);
    }

}
