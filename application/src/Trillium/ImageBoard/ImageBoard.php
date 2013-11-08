<?php

/**
 * Part of the Trillium
 *
 * @package Trillium
 */

namespace Trillium\ImageBoard;

use Trillium\ImageBoard\Service\Board;
use Trillium\ImageBoard\Service\Common;
use Trillium\ImageBoard\Service\Thread;

/**
 * ImageBoard Class
 *
 * @package Trillium\ImageBoard
 */
trait ImageBoard {

    /**
     * Get ib board object
     * @return Board
     */
    public function ibBoard() {
        return $this['imageboard.board'];
    }

    /**
     * Get ib thread object
     * @return Thread
     */
    public function ibThread() {
        return $this['imageboard.thread'];
    }

    /**
     * Get ib post object
     * @return mixed
     */
    public function ibPost() {
        return $this['imageboard.post'];
    }

    /**
     * Get ib common object
     * @return Common
     */
    public function ibCommon() {
        return $this['imageboard.common'];
    }

} 