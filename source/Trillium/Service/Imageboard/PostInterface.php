<?php

/**
 * Part of the Trillium
 *
 * @author  Kilte Leichnam <nwotnbm@gmail.com>
 * @package Trillium
 */

namespace Trillium\Service\Imageboard;

/**
 * PostInterface Interface
 *
 * @package Trillium\Service\Imageboard
 */
interface PostInterface
{

    /**
     * Creates a post
     * Returns ID of created post
     *
     * @param int    $thread  ID of parent thread
     * @param string $message A message
     *
     * @return int
     */
    public function create($thread, $message);

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

}
