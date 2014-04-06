<?php

/**
 * Part of the Trillium
 *
 * @author  Kilte Leichnam <nwotnbm@gmail.com>
 * @package Trillium
 */

namespace Trillium\Service\Imageboard;

use Trillium\Service\Imageboard\Exception\BoardNotFoundException;

/**
 * BoardInterface Interface
 *
 * @package Trillium\Service\Imageboard
 */
interface BoardInterface
{

    /**
     * Creates a board
     *
     * @param string $name    Name
     * @param string $summary Summary
     *
     * @return void
     */
    public function create($name, $summary);

    /**
     * Returns data of a board
     *
     * @param string $name Name
     *
     * @throws BoardNotFoundException Board does not exists
     * @return array
     */
    public function get($name);

    /**
     * Updates a board
     *
     * @param string $newName    New name
     * @param string $newSummary New summary
     * @param string $name       Name (to get board)
     *
     * @return void
     */
    public function update($newName, $newSummary, $name);

    /**
     * Removes a board by name
     *
     * @param string $name Name
     *
     * @return int Affected rows
     */
    public function delete($name);

    /**
     * Returns boards list
     *
     * @return array
     */
    public function listing();

    /**
     * Whether board exists
     *
     * @param string $name Name
     *
     * @return boolean
     */
    public function isExists($name);

}
