<?php

/**
 * Part of the Trillium
 *
 * @package Trillium
 */

namespace Trillium\ImageBoard;

use Trillium\ImageBoard\Service\ImageBoard;
use Trillium\ImageBoard\Service\Message;

/**
 * ImageBoardTrait Trait
 *
 * Allows autocomplete
 *
 * @package Trillium\ImageBoard
 */
trait ImageBoardTrait
{
    /**
     * Returns ImageBoard service
     *
     * @return ImageBoard
     */
    public function aib()
    {
        return $this['imageboard'];
    }

    /**
     * Returns message service
     *
     * @return Message
     */
    public function aibMessage()
    {
        return $this['imageboard.message'];
    }

}
