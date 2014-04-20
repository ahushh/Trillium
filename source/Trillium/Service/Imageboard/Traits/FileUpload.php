<?php

/**
 * Part of the Trillium
 *
 * @author  Kilte Leichnam <nwotnbm@gmail.com>
 * @package Trillium
 */

namespace Trillium\Service\Imageboard\Traits;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;

/**
 * FileUpload Trait
 *
 * @property-read \Trillium\Service\Image\Manager             $manager
 * @property-read \Trillium\Service\Image\Validator           $imageValidator
 * @property-read \Trillium\Service\Imageboard\ImageInterface $image
 *
 * @package Trillium\Service\Imageboard\Traits
 */
trait FileUpload
{

    /**
     * Validates a uploaded file
     *
     * @param Request $request A request instance
     *
     * @return array
     */
    protected function validateFile(Request $request)
    {
        $error = [];
        $file  = $request->files->get('file');
        if ($file instanceof UploadedFile) {
            $error = $this->imageValidator->performCheck($file);
        }

        return $error;
    }

    /**
     * Upload a file
     *
     * @param Request $request A request instance
     * @param string  $board   Board name
     * @param int     $thread  Thread ID
     * @param int     $post    Post ID
     *
     * @throws \RuntimeException
     *
     * @return void
     */
    protected function uploadFile(Request $request, $board, $thread, $post)
    {
        $file = $request->files->get('file');
        if ($file instanceof UploadedFile) {
            $ext         = $file->getClientOriginalExtension();
            $ext         = $ext === 'jpg' ? 'jpeg' : $ext;
            $type        = $this->getImageTypeByExtension($ext);
            $imageCreate = 'imagecreatefrom' . $ext;
            $image       = @$imageCreate($file->getRealPath());
            if ($image === false) {
                throw new \RuntimeException(sprintf('Unable to create a thumbnail: %s', error_get_last()['message']));
            }
            $target = $thread . '/' . $post;
            $this->manager->save($file, $target)->thumbnail($image, $type, $target);
            $this->image->create(
                $board,
                $thread,
                $post,
                $file->getClientOriginalExtension(),
                imagesx($image),
                imagesy($image),
                (int) round($file->getClientSize() / 1024, 0)
            );
        }
    }

    /**
     * Returns image type by extension
     *
     * @param string $ext
     *
     * @throws \InvalidArgumentException
     *
     * @return int
     */
    private function getImageTypeByExtension($ext)
    {
        switch ($ext) {
            case 'jpeg':
                return IMAGETYPE_JPEG;
            case 'png':
                return IMAGETYPE_PNG;
            case 'gif':
                return IMAGETYPE_GIF;
        }
        throw new \InvalidArgumentException(sprintf('Wrong extension given: %s', $ext));
    }

}
