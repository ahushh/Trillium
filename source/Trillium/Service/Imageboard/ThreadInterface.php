<?php

/**
 * Part of the Trillium
 *
 * @author  Kilte Leichnam <nwotnbm@gmail.com>
 * @package Trillium
 */

namespace Trillium\Service\Imageboard;

use Trillium\Service\Imageboard\Exception\ThreadNotFoundException;

/**
 * ThreadInterface Interface
 *
 * @package Trillium\Service\Imageboard
 */
interface ThreadInterface
{

    /**
     * Creates a thread
     * Returns ID of created thread
     *
     * @param string $title Thread title
     * @param string $board Parent board
     *
     * @return int
     */
    public function create($title, $board);

    /**
     * Renames thread
     *
     * @param int    $id    ID of a thread
     * @param string $title New title
     *
     * @return void
     */
    public function rename($id, $title);

    /**
     * Removes a thread
     * Returns number of affected rows
     *
     * @param int $id ID of a thread
     *
     * @return int
     */
    public function remove($id);

    /**
     * Remove all threads of a board
     * Returns number of affected rows
     *
     * @param string $board Name of the board
     *
     * @return int
     */
    public function removeBoard($board);

    /**
     * Returns list of threads for a given board
     *
     * @param string $board Name of a board
     *
     * @return array
     */
    public function listing($board);

    /**
     * Returns thread data
     *
     * @param int $id ID of thread
     *
     * @throws ThreadNotFoundException
     *
     * @return array
     */
    public function get($id);

    /**
     * Whether thread exists
     *
     * @param int $id ID of thread
     *
     * @return boolean
     */
    public function isExists($id);

}
