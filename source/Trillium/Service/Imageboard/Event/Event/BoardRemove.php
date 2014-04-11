<?php

/**
 * Part of the Trillium
 *
 * @author  Kilte Leichnam <nwotnbm@gmail.com>
 * @package Trillium
 */

namespace Trillium\Service\Imageboard\Event\Event;

use Symfony\Component\EventDispatcher\Event;

/**
 * BoardRemove Class
 *
 * @package Trillium\Service\Imageboard\Event\Event
 */
class BoardRemove extends Event
{

    /**
     * @var string Name of the board
     */
    private $board;

    /**
     * Constructor
     *
     * @param string $board Name of the board
     *
     * @return self
     */
    public function __construct($board)
    {
        $this->board = $board;
    }

    /**
     * Returns name of the board
     *
     * @return string
     */
    public function getBoard()
    {
        return $this->board;
    }

}
