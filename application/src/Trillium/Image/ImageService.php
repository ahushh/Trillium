<?php

/**
 * Part of the Trillium
 *
 * @package Trillium
 */

namespace Trillium\Image;

/**
 * ImageService Class
 *
 * Resize and save image
 *
 * @package Trillium\Image
 */
class ImageService {

    /**
     * @var resource Image
     */
    private $resource;

    /**
     * @var int Type if the image
     */
    private $type;

    /**
     * @var string Extension
     */
    private $extension;

    /**
     * Create instance
     *
     * @param string $path Path to the image
     *
     * @throws \RuntimeException
     * @return ImageService
     */
    public function __construct($path) {
        $imageInfo = getimagesize($path);
        $this->type = $imageInfo[2];
        switch ($this->type) {
            case IMAGETYPE_GIF:
                $this->extension = 'gif';
                $this->resource = imagecreatefromgif($path);
                break;
            case IMAGETYPE_JPEG:
                $this->extension = 'jpg';
                $this->resource = imagecreatefromjpeg($path);
                break;
            case IMAGETYPE_PNG:
                $this->extension = 'png';
                $this->resource = imagecreatefrompng($path);
                break;
            default:
                throw new \RuntimeException('Unsupported type of the image');
        }
    }

    /**
     * Returns type of the image
     *
     * @return int
     */
    public function type() {
        return $this->type;
    }

    /**
     * Returns resource
     *
     * @return resource
     */
    public function resource() {
        return $this->resource;
    }

    /**
     * Returns width of the current image
     *
     * @return int
     */
    public function width() {
        return imagesx($this->resource);
    }

    /**
     * Returns height of the current image
     *
     * @return int
     */
    public function height() {
        return imagesy($this->resource);
    }

    /**
     * Resize by width
     *
     * @param int $width Width
     *
     * @return resource
     */
    public function resizeWidth($width) {
        return $this->resize($width, $this->height() * ($width / $this->width()));
    }

    /**
     * Resize by height
     *
     * @param int $height Height
     *
     * @return resource
     */
    public function resizeHeight($height) {
        return $this->resize($this->width() * ($height / $this->height()), $height);
    }

    /**
     * Resize image
     *
     * @param int $width  Width
     * @param int $height Height
     *
     * @return resource
     */
    public function resize($width, $height) {
        $resized = imagecreatetruecolor($width, $height);
        imagecopyresampled($resized, $this->resource, 0, 0, 0, 0, $width, $height, $this->width(), $this->height());
        return $resized;
    }

    /**
     * Save image
     * If resource is not given, current resource will be used
     *
     * @param string        $path        Destination path without extension
     * @param resource|null $resource    Resource
     * @param int           $type        Type
     * @param int           $compression Compression
     * @param mixed         $permissions Permissions
     *
     * @return void
     * @throws \RuntimeException
     */
    public function save($path, $resource = null, $type = IMAGETYPE_JPEG, $compression = 100, $permissions = null) {
        $resource = $resource === null ? $this->resource : $resource;
        switch ($type) {
            case IMAGETYPE_GIF:
                $path = $path . '.gif';
                imagegif($resource, $path);
                break;
            case IMAGETYPE_JPEG:
                $path = $path . '.jpg';
                imagejpeg($resource, $path, $compression);
                break;
            case IMAGETYPE_PNG:
                $path = $path . '.png';
                imagepng($resource, $path, $compression);
                break;
            default:
                throw new \RuntimeException('Unsupported type of the image');
        }
        if ($permissions !== null) {
            chmod($path, $permissions);
        }
    }

} 