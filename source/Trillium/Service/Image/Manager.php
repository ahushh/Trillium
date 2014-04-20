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
    const THUMBNAIL_POSTFIX = '_preview.jpeg';

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
     * @var Resize
     */
    private $resize;

    /**
     * Constructor
     *
     * @param Filesystem $fs      Filesystem instance
     * @param Resize     $resize  Resize instance
     * @param array      $options Options
     *
     * @return self
     */
    public function __construct(Filesystem $fs, Resize $resize, array $options = [])
    {
        $this->fs                       = $fs;
        $this->resize                   = $resize;
        $this->options                  = array_replace($this->options, $options);
        $this->options['directory']     = rtrim($this->options['directory'], '\/') . DIRECTORY_SEPARATOR;
        $this->options['thumb_quality'] = (int) $this->options['thumb_quality'];
        if (!is_dir($this->options['directory'])) {
            throw new \InvalidArgumentException(sprintf('Directory "%s" does not exists', $this->options['directory']));
        }
        if ($this->options['thumb_quality'] < 0 || $this->options['thumb_quality'] > 100) {
            throw new \InvalidArgumentException(
                sprintf('Quality must be between %u and %u, %u given', 0, 100, $this->options['thumb_quality'])
            );
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
        $target = $this->options['directory'] . ltrim($target, '\/') . '.' . $origin->getClientOriginalExtension();
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
     * @return $this
     */
    public function thumbnail($origin, $type, $target)
    {
        $image  = $this->resize
            ->setImage($origin, $type)
            ->resize($this->options['thumb_width'], $this->options['thumb_height']);
        $target = $this->options['directory'] . ltrim($target, '\/') . self::THUMBNAIL_POSTFIX;
        imagejpeg($image, $target, $this->options['thumb_quality']);

        return $this;
    }

    /**
     * Removes a files
     *
     * @param array|string|\Traversable $files Files
     *
     * @return $this
     */
    public function remove($files)
    {
        if (is_array($files)) {
            $files = array_map(
                function ($file) {
                    return $this->options['directory'] . ltrim($file, '\/');
                },
                $files
            );
        } elseif (is_string($files)) {
            $files = $this->options['directory'] . ltrim($files, '\/');
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
        $this->fs->mkdir($this->options['directory'] . trim($name, '\/'), $mode);

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
        return $this->options['directory'] . trim($name, '\/') . DIRECTORY_SEPARATOR;
    }

}
