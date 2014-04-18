<?php

/**
 * Part of the Trillium
 *
 * @author  Kilte Leichnam <nwotnbm@gmail.com>
 * @package Trillium
 */

namespace Trillium\Service\Image;

use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Image Class
 *
 * @package Trillium\Service\Image
 */
class Image
{

    /**
     * @var UploadedFile
     */
    private $file;

    /**
     * @var resource An image
     */
    private $image;

    /**
     * @var int Width of the uploaded image
     */
    private $imageWidth;

    /**
     * @var int Height of the uploaded image
     */
    private $imageHeight;

    /**
     * @var int Max file size (bytes)
     */
    private $maxSize;

    /**
     * @var array Occurred errors
     */
    private $error;

    /**
     * @var array List of allowed extensions
     */
    private $allowedExtensions;

    /**
     * @var array List of allowed MIME types
     */
    private $allowedMimes;

    /**
     * @var int Max width (px)
     */
    private $maxWidth;

    /**
     * @var int Max height (px)
     */
    private $maxHeight;

    /**
     * @var int Thumbnail width (px)
     */
    private $thumbWidth;

    /**
     * @var int Thumbnail height (px)
     */
    private $thumbHeight;

    /**
     * @var string Directory for uploaded files
     */
    private $directory;

    /**
     * @var string Directory for thumbnails
     */
    private $thumbDir;

    /**
     * @var int Thumbnail quality
     */
    private $q;

    /**
     * Constructor
     *
     * @param string $directory   Directory for uploaded files
     * @param string $thumbDir    Directory for thumbnails
     * @param int    $maxSize     Max file size (Mb)
     * @param int    $maxWidth    Max image width (px)
     * @param int    $maxHeight   Max image height (px)
     * @param int    $thumbWidth  Thumbnail width (px)
     * @param int    $thumbHeight Thumbnail height (px)
     * @param int    $q           Thumbnail quality
     *
     * @return self
     */
    public function __construct($directory, $thumbDir, $maxSize, $maxWidth, $maxHeight, $thumbWidth, $thumbHeight, $q)
    {
        $this->maxSize           = $maxSize * 1024 * 1024;
        $this->error             = [];
        $this->allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];
        $this->allowedMimes      = ['image/jpeg', 'image/png', 'image/gif', 'image/x-png'];
        $this->maxWidth          = $maxWidth;
        $this->maxHeight         = $maxHeight;
        $this->thumbWidth        = $thumbWidth;
        $this->thumbHeight       = $thumbHeight;
        $this->directory         = $directory;
        $this->thumbDir          = $thumbDir;
        $this->q                 = $q;
    }

    /**
     * Sets a request
     *
     * @param UploadedFile $file
     *
     * @return $this
     */
    public function setFile(UploadedFile $file)
    {
        $this->file = $file;

        return $this;
    }

    /**
     * Cleanup
     *
     * @return $this
     */
    public function cleanUp()
    {
        $this->file  = null;
        $this->image = null;
        $this->error = [];

        return $this;
    }

    /**
     * Validates the file
     *
     * @return $this
     */
    public function validate()
    {
        if (!$this->file->isValid()) {
            $this->error[] = $this->file->getErrorMessage();
        }
        if (!in_array($this->file->getClientOriginalExtension(), $this->allowedExtensions)) {
            $this->error[] = sprintf(
                'Illegal extension: %s. Allowed: %s',
                $this->file->getClientOriginalExtension(),
                implode(', ', $this->allowedExtensions)
            );
        } elseif (!in_array($this->file->getMimeType(), $this->allowedMimes)) {
            $this->error[] = sprintf(
                'Illegal MIME type: %s. Allowed: ',
                $this->file->getMimeType(),
                implode(', ', $this->allowedMimes)
            );
        } else {
            $imageSize = @getimagesize($this->file->getRealPath());
            if ($imageSize === false) {
                $this->error[] = error_get_last()['message'];
            } else {
                $this->imageWidth  = $imageSize[0];
                $this->imageHeight = $imageSize[1];
                if ($this->imageWidth > $this->maxWidth) {
                    $this->error[] = sprintf('Width of the image exceeds %upx', $this->maxWidth);
                }
                if ($this->imageHeight > $this->maxHeight) {
                    $this->error[] = sprintf('Height of the image exceeds %upx', $this->maxHeight);
                }
                switch ($imageSize[2]) {
                    case IMAGETYPE_JPEG:
                        $this->image = imagecreatefromjpeg($this->file->getRealPath());
                        break;
                    case IMAGETYPE_GIF:
                        $this->image = imagecreatefromgif($this->file->getRealPath());
                        break;
                    case IMAGETYPE_PNG:
                        $this->image = imagecreatefrompng($this->file->getRealPath());
                        break;
                    default:
                        $this->error[] = 'Illegal type';
                }
            }
        }
        if ($this->file->getSize() > $this->maxSize) {
            $this->error[] = sprintf('File size exceeds %u Kb', $this->maxSize / 1024);
        }

        return $this;
    }

    /**
     * Moves the file to a new location
     * Creates a jpeg thumbnail
     *
     * @param string $name  New filename
     * @param string $thumb Thumbnail filename
     *
     * @throws \LogicException
     * @throws \Symfony\Component\HttpFoundation\File\Exception\FileException
     *
     * @return $this
     */
    public function upload($name, $thumb)
    {
        if (!empty($this->error)) {
            throw new \LogicException('File is invalid');
        }
        if ($this->imageWidth > $this->thumbWidth || $this->imageHeight > $this->thumbHeight) {
            // create thumbnail
            list($newWidth, $newHeight) = $this->calculateThumbnailSize();
            $image = $this->thumbnail($newWidth, $newHeight);
        } else {
            // copy original
            $image = $this->image;
        }
        imagejpeg($image, $this->thumbDir . $thumb . '.jpeg', $this->q);
        $this->file->move($this->directory, $name . '.' . $this->file->getClientOriginalExtension());

        return $this;
    }

    /**
     * Calculates thumbnail resolution
     *
     * @return array [width, height]
     */
    private function calculateThumbnailSize()
    {
        $xRatio    = $this->thumbWidth / $this->imageWidth;
        $yRatio    = $this->thumbHeight / $this->imageHeight;
        $ratio     = min($xRatio, $yRatio);
        $useXRatio = ($xRatio == $ratio);
        $newWidth  = $useXRatio ? $this->thumbWidth : floor($this->imageWidth * $ratio);
        $newHeight = !$useXRatio ? $this->thumbHeight : floor($this->imageHeight * $ratio);

        return [$newWidth, $newHeight];
    }

    /**
     * Creates the thumbnail
     *
     * @param int $newWidth  New width
     * @param int $newHeight New height
     *
     * @return resource
     */
    private function thumbnail($newWidth, $newHeight)
    {
        $imageCreate = 'imagecreate' . ($this->file->getExtension() == 'gif' ? '' : 'truecolor');
        $image       = $imageCreate($newWidth, $newHeight);
        // Save transparent
        if ($this->file->getExtension() == 'gif') {
            imagecolortransparent($image, imagecolorallocate($image, 0, 0, 0));
        } elseif ($this->file->getExtension() == 'png') {
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
            $this->imageWidth,
            $this->imageHeight
        );

        return $image;
    }

    /**
     * Returns occurred errors
     *
     * @return array
     */
    public function getError()
    {
        return $this->error;
    }

    /**
     * Removes an image and thumbnail
     *
     * @param int    $name  Filename
     * @param string $ext   File extension
     * @param string $thumb Thumbnail name
     *
     * @return void
     */
    public function remove($name, $ext, $thumb)
    {
        $file  = $this->directory . $name . '.' . $ext;
        $thumb = $this->directory . $thumb . '.jpeg';
        foreach ([$file, $thumb] as $image) {
            if (is_file($image)) {
                unlink($image);
            }
        }
    }

    /**
     * Returns the width of an image
     *
     * @return int
     */
    public function getImageWidth()
    {
        return $this->imageWidth;
    }

    /**
     * Returns the height of an image
     *
     * @return int
     */
    public function getImageHeight()
    {
        return $this->imageHeight;
    }

}
