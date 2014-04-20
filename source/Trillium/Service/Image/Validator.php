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
 * Validator Class
 *
 * @package Trillium\Service\Image
 */
class Validator
{

    /**
     * @var array Permissions
     */
    protected $permissions = [
        'allowed_extensions' => ['jpg', 'jpeg', 'png', 'gif'],
        'allowed_mimes'      => ['image/jpeg', 'image/png', 'image/gif', 'image/x-png'],
        'allowed_types'      => [IMAGETYPE_JPEG, IMAGETYPE_GIF, IMAGETYPE_PNG],
        'max_width'          => 5000,
        'max_height'         => 5000,
        'max_size'           => 5242880, // Max file size in bytes
    ];

    /**
     * Constructor
     *
     * @param array $permissions Permissions
     *
     * @return self
     */
    public function __construct(array $permissions)
    {
        $this->permissions = array_replace($this->permissions, $permissions);
    }

    /**
     * Checks, whether uploaded file is valid.
     *
     * Returns array of occurred errors.
     * If no errors occurred, array will be empty.
     *
     * @param UploadedFile $file Uploaded file
     *
     * @return array
     */
    public function performCheck(UploadedFile $file)
    {
        $error = [];
        if (!$file->isValid()) {
            $error[] = $file->getErrorMessage();
        }
        if (!in_array($file->getClientOriginalExtension(), $this->permissions['allowed_extensions'])) {
            $error[] = sprintf(
                'Illegal extension: %s. Allowed: %s',
                $file->getClientOriginalExtension(),
                implode(', ', $this->permissions['allowed_extensions'])
            );
        } elseif (!in_array($file->getClientMimeType(), $this->permissions['allowed_mimes'])) {
            $error[] = sprintf(
                'Illegal MIME type: %s. Allowed: ',
                $file->getClientMimeType(),
                implode(', ', $this->permissions['allowed_mimes'])
            );
        } else {
            $imageSize = @getimagesize($file->getRealPath());
            if ($imageSize === false) {
                $error[] = error_get_last()['message'];
            } else {
                $imageWidth  = $imageSize[0];
                $imageHeight = $imageSize[1];
                if ($imageWidth > $this->permissions['max_width']) {
                    $error[] = sprintf('Width of the image exceeds %upx', $this->permissions['max_width']);
                }
                if ($imageHeight > $this->permissions['max_height']) {
                    $error[] = sprintf('Height of the image exceeds %upx', $this->permissions['max_height']);
                }
                if (!in_array($imageSize[2], $this->permissions['allowed_types'])) {
                    $error[] = 'Illegal type';
                }
            }
        }
        if ($file->getClientSize() > $this->permissions['max_size']) {
            $error[] = sprintf('File size exceeds %u Kb', $this->permissions['max_size'] / 1024);
        }

        return $error;
    }

}
