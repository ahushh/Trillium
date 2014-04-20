<?php

/**
 * Part of the Trillium
 *
 * @author  Kilte Leichnam <nwotnbm@gmail.com>
 * @package Trillium
 */

namespace Trillium\Service\Image;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Manager Class
 *
 * @package Trillium\Service\Image
 */
class Manager
{

    /**
     * Postfix for thumbnail filename
     */
    const THUMBNAIL_POSTFIX = '_preview.png';

    /**
     * @var Filesystem Filesystem instance
     */
    private $fs;

    /**
     * @var array Options
     */
    private $options = [
        'directory'     => null,
        'thumb_quality' => 90,
        'thumb_width'   => 320,
        'thumb_height'  => 240
    ];

    /**
     * @var Resize Resize instance
     */
    private $resize;

    /**
     * Constructor
     *
     * @param Filesystem $fs      Filesystem instance
     * @param Resize     $resize  Resize instance
     * @param array      $options Options
     *
     * @throws \InvalidArgumentException
     *
     * @return self
     */
    public function __construct(Filesystem $fs, Resize $resize, array $options = [])
    {
        $this->fs                       = $fs;
        $this->resize                   = $resize;
        $this->options                  = array_replace($this->options, $options);
        $this->options['directory']     = rtrim($this->options['directory'], '\/') . DIRECTORY_SEPARATOR;
        if (!is_dir($this->options['directory'])) {
            throw new \InvalidArgumentException(sprintf('Directory "%s" does not exists', $this->options['directory']));
        }
    }

    /**
     * Save a file
     *
     * @param UploadedFile $origin Original file
     * @param string       $target Target name
     *
     * @return $this
     */
    public function save(UploadedFile $origin, $target)
    {
        $target = $this->getTargetPath($target) . '.' . $origin->getClientOriginalExtension();
        $this->fs->copy($origin->getRealPath(), $target);

        return $this;
    }

    /**
     * Creates a thumbnail
     *
     * @param resource $origin An image
     * @param int      $type   Image type (IMAGETYPE_XXX)
     * @param string   $target Filename
     *
     * @throws \InvalidArgumentException
     *
     * @return $this
     */
    public function thumbnail($origin, $type, $target)
    {
        if (!is_resource($origin)) {
            throw new \InvalidArgumentException('Illegal image');
        }
        $target = $this->getTargetPath($target) . self::THUMBNAIL_POSTFIX;
        if (imagesx($origin) > $this->options['thumb_width'] || imagesy($origin) > $this->options['thumb_height']) {
            $image  = $this->resize
                ->setImage($origin, $type)
                ->resize($this->options['thumb_width'], $this->options['thumb_height']);
        } else {
            $image = $origin;
            $this->resize->saveTransparent($image, $type);
        }

        imagepng($image, $target);

        return $this;
    }

    /**
     * Removes a files
     *
     * @param array|string $files Files
     *
     * @return $this
     */
    public function remove($files)
    {
        if (is_array($files)) {
            $files = array_map(
                function ($file) {
                    return $this->getTargetPath($file);
                },
                $files
            );
        } elseif (is_string($files)) {
            $files = $this->getTargetPath($files);
        }
        $this->fs->remove($files);

        return $this;
    }

    /**
     * Create a directory
     *
     * @param string $name Name
     * @param int    $mode Mode
     *
     * @return $this
     */
    public function makeDirectory($name, $mode = 0777)
    {
        $this->fs->mkdir($this->getTargetPath($name), $mode);

        return $this;
    }

    /**
     * Returns absolute path to directory
     *
     * @param string $name Name
     *
     * @return string
     */
    public function getDirectory($name)
    {
        return $this->getTargetPath($name) . DIRECTORY_SEPARATOR;
    }

    /**
     * Returns a target path
     *
     * @param string $target Name
     *
     * @return string
     */
    private function getTargetPath($target)
    {
        return $this->options['directory'] . trim($target, '\/');
    }

}
