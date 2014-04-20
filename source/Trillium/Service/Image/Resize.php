<?php

/**
 * Part of the Trillium
 *
 * @author  Kilte Leichnam <nwotnbm@gmail.com>
 * @package Trillium
 */

namespace Trillium\Service\Image;

/**
 * Resize Class
 *
 * @package Trillium\Service\Image
 */
class Resize
{

    /**
     * @var resource Image to resize
     */
    protected $image;

    /**
     * @var int Original width
     */
    protected $originalWidth;

    /**
     * @var int Original height
     */
    protected $originalHeight;

    /**
     * @var int Image type
     */
    protected $type;

    /**
     * Sets an image
     *
     * @param resource $image An image
     * @param int      $type  Type of the given image (IMAGETYPE_XXX)
     *
     * @throws \InvalidArgumentException
     * @throws \RuntimeException
     *
     * @return $this
     */
    public function setImage($image, $type)
    {
        if (!is_resource($image)) {
            throw new \InvalidArgumentException('Image is not resource');
        }
        if (!in_array($type, [IMAGETYPE_PNG, IMAGETYPE_GIF, IMAGETYPE_JPEG])) {
            throw new \InvalidArgumentException('Unsupported type of the image');
        }
        $this->image          = $image;
        $this->type           = $type;
        $this->originalWidth  = imagesx($image);
        $this->originalHeight = imagesy($image);
        if ($this->originalHeight === false || $this->originalWidth === false) {
            throw new \RuntimeException('Could not to get image size');
        }

        return $this;
    }

    /**
     * Cleanup
     *
     * @return $this
     */
    public function cleanUp()
    {
        $this->image          = null;
        $this->originalWidth  = null;
        $this->originalHeight = null;
        $this->type           = null;

        return $this;
    }

    /**
     * Resize the image
     *
     * @param int $width  New width
     * @param int $height New height
     *
     * @return resource
     */
    public function resize($width, $height)
    {
        list($newWidth, $newHeight) = $this->calculateResolution($width, $height);
        $imageCreate = 'imagecreate' . ($this->type == IMAGETYPE_GIF ? '' : 'truecolor');
        $image       = $imageCreate($newWidth, $newHeight);
        // Save transparent
        if ($this->type == IMAGETYPE_GIF) {
            imagecolortransparent($image, imagecolorallocate($image, 0, 0, 0));
        } elseif ($this->type == IMAGETYPE_PNG) {
            imagealphablending($image, false);
            imagesavealpha($image, true);
        }
        imagecopyresampled(
            $image,
            $this->image,
            0,
            0,
            0,
            0,
            $newWidth,
            $newHeight,
            $this->originalWidth,
            $this->originalHeight
        );

        return $image;
    }

    /**
     * Calculates thumbnail resolution
     * Returns new width and height
     *
     * @param int $width  Width
     * @param int $height Height
     *
     * @return array [width, height]
     */
    protected function calculateResolution($width, $height)
    {
        $xRatio    = $width / $this->originalWidth;
        $yRatio    = $height / $this->originalHeight;
        $ratio     = min($xRatio, $yRatio);
        $useXRatio = ($xRatio == $ratio);
        $newWidth  = $useXRatio ? $width : floor($this->originalWidth * $ratio);
        $newHeight = !$useXRatio ? $height : floor($this->originalHeight * $ratio);

        return [$newWidth, $newHeight];
    }

}
