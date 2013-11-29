<?php

/**
 * Part of the Trillium
 *
 * @package Trillium
 */

namespace Trillium\ImageBoard;

use Trillium\ImageBoard\Service\Common;
use Trillium\ImageBoard\Service\ImageBoard;

/**
 * ImageBoardTrait Trait
 *
 * @package Trillium\ImageBoard
 */
trait ImageBoardTrait {

    /**
     * Returns ImageBoard service
     *
     * @return ImageBoard
     */
    public function aib() {
        return $this['imageboard'];
    }

    /**
     * Get ib common object
     *
     * @return Common
     */
    public function ibCommon() {
        return $this['imageboard.common'];
    }

} 