<?php

/**
 * Part of the Trillium
 *
 * @package Trillium
 */

namespace Trillium\Markup;

/**
 * MarkupTrait Trait
 *
 * @package Trillium\Markup
 */
trait MarkupTrait {

    /**
     * Returns Markup object
     *
     * @return Markup
     */
    public function markup() {
        return $this['markup'];
    }

} 