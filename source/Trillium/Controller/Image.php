<?php

/**
 * Part of the Trillium
 *
 * @author  Kilte Leichnam <nwotnbm@gmail.com>
 * @package Trillium
 */

namespace Trillium\Controller;

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
        $image = $this->image->get($id);
        if (is_array($image)) {
            $this->image->remove($id);
            $this->imageService->remove($image['thread'] . '/' . $image['post'], $image['ext'], $image['thread'] . '/' . $image['post'] . '_preview');
            $result = ['success' => 'Image removed'];
        } else {
            $result = ['error' => 'Image does not exists', '_status' => 404];
        }

        return $result;
    }

}
