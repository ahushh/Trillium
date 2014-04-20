<?php

/**
 * Part of the Trillium
 *
 * @author  Kilte Leichnam <nwotnbm@gmail.com>
 * @package Trillium
 */

namespace Trillium\Controller;

use Trillium\Service\Image\Manager;
use Trillium\Service\Imageboard\Exception\ImageNotFoundException;

/**
 * Image Class
 *
 * @package Trillium\Controller
 */
class Image extends Controller
{

    /**
     * Removes an image
     *
     * @param int $id Post ID
     *
     * @return array
     */
    public function remove($id)
    {
        try {
            $image = $this->image->get($id);
            $this->image->remove($id);
            $this->imageManager->remove(
                [
                    $image['thread'] . '/' . $image['post'] . '.' . $image['ext'],
                    $image['thread'] . '/' . $image['post'] . Manager::THUMBNAIL_POSTFIX
                ]
            );
            $result = ['success' => 'Image removed'];
        } catch (ImageNotFoundException $e) {
            $result = ['error' => $e->getMessage(), '_status' => 404];
        }

        return $result;
    }

}
