<?php

/**
 * Part of the Trillium
 *
 * @package Trillium
 */

namespace Trillium\Image;

/**
 * ImageTrait Trait
 *
 * @package Trillium\Image
 */
trait ImageTrait
{
    /**
     * Image Service
     *
     * @param string $path Path to the image
     *
     * @return ImageService
     */
    public function image($path)
    {
        return $this['image']($path);
    }

}
