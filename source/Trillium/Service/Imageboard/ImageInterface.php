<?php

/**
 * Part of the Trillium
 *
 * @author  Kilte Leichnam <nwotnbm@gmail.com>
 * @package Trillium
 */

namespace Trillium\Service\Imageboard;

/**
 * ImageInterface Interface
 *
 * @package Trillium\Service\Imageboard
 */
interface ImageInterface
{

    /**
     * Creates an image
     *
     * @param string $board  Board name
     * @param int    $thread Thread ID
     * @param int    $post   Post ID
     * @param string $ext    Extension
     *
     * @return void
     */
    public function create($board, $thread, $post, $ext);

    /**
     * Returns an image by post ID
     *
     * @param int $post Post ID
     *
     * @return array
     */
    public function get($post);

    /**
     * Returns images attached to board
     *
     * @param string $board Board name
     *
     * @return array
     */
    public function getBoard($board);

    /**
     * Returns images attached to thread
     *
     * @param int $thread Thread ID
     *
     * @return array
     */
    public function getThread($thread);

    /**
     * Removes an image attached to post
     *
     * Returns number of affected rows
     *
     * @param int $post Post ID
     *
     * @return int
     */
    public function remove($post);

    /**
     * Removes images attached to thread
     *
     * Returns number of affected rows
     *
     * @param int $thread Thread ID
     *
     * @return int
     */
    public function removeThread($thread);

    /**
     * Removes images attached to board
     *
     * Returns number of affected rows
     *
     * @param string $board Board name
     *
     * @return int
     */
    public function removeBoard($board);

    /**
     * Moves images between boards
     *
     * @param string $old Old board
     * @param string $new New board
     *
     * @return void
     */
    public function move($old, $new);

}
