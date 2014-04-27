<?php

/**
 * Part of the Trillium
 *
 * @author  Kilte Leichnam <nwotnbm@gmail.com>
 * @package Trillium
 */

namespace Trillium\Service\Imageboard;

use Trillium\Service\Imageboard\Exception\PostNotFoundException;

/**
 * PostInterface Interface
 *
 * @package Trillium\Service\Imageboard
 */
interface PostInterface
{

    /**
     * Returns a post data
     *
     * @param int $post Post ID
     *
     * @throws PostNotFoundException
     *
     * @return array
     */
    public function get($post);

    /**
     * Creates a post
     * Returns ID of created post
     *
     * @param string $board     Name of parent board
     * @param int    $thread    ID of parent thread
     * @param string $message   A message
     * @param int    $timestamp Timestamp
     *
     * @return int
     */
    public function create($board, $thread, $message, $timestamp);

    /**
     * Remove all posts of a board
     * Returns number of affected rows
     *
     * @param string $board Name of the board
     *
     * @return int
     */
    public function removeBoard($board);

    /**
     * Remove all post of a thread
     * Returns number of affected rows
     *
     * @param int $id Thread ID
     *
     * @return int
     */
    public function removeThread($id);

    /**
     * Moves posts between boards
     *
     * @param string $old Old board
     * @param string $new New board
     *
     * @return void
     */
    public function move($old, $new);

    /**
     * Removes a post
     * Returns number of affected rows
     *
     * @param int $id Post ID
     *
     * @return int
     */
    public function remove($id);

    /**
     * Returns list of the posts
     *
     * @param int $thread Thread ID
     *
     * @return mixed
     */
    public function listing($thread);

}
