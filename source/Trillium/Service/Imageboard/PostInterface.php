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

}
