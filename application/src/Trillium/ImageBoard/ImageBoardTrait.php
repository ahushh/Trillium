<?php

/**
 * Part of the Trillium
 *
 * @package Trillium
 */

namespace Trillium\ImageBoard;

use Trillium\ImageBoard\Service\Board;
use Trillium\ImageBoard\Service\Common;
use Trillium\ImageBoard\Service\Image;
use Trillium\ImageBoard\Service\Markup;
use Trillium\ImageBoard\Service\Post;
use Trillium\ImageBoard\Service\Thread;

/**
 * ImageBoardTrait Trait
 *
 * @package Trillium\ImageBoard
 */
trait ImageBoardTrait {

    /**
     * Get ib board object
     *
     * @return Board
     */
    public function ibBoard() {
        return $this['imageboard.board'];
    }

    /**
     * Get ib thread object
     *
     * @return Thread
     */
    public function ibThread() {
        return $this['imageboard.thread'];
    }

    /**
     * Get ib post object
     *
     * @return Post
     */
    public function ibPost() {
        return $this['imageboard.post'];
    }

    /**
     * Get ib image object
     *
     * @return Image
     */
    public function ibImage() {
        return $this['imageboard.image'];
    }

    /**
     * Get ib common object
     *
     * @return Common
     */
    public function ibCommon() {
        return $this['imageboard.common'];
    }

    /**
     * Get ib markup object
     *
     * @return Markup
     */
    public function ibMarkup() {
        return $this['imageboard.markup'];
    }

} 