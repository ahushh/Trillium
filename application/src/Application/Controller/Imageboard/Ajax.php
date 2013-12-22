<?php

/**
 * Part of the Trillium
 *
 * @package Trillium
 */

namespace Application\Controller\Imageboard;

use Application\Controller\ImageBoard;
use Trillium\ImageBoard\Service\Image\Image;

/**
 * Ajax Class
 *
 * Ajax requests handler
 *
 * @package Application\Controller\Imageboard
 */
class Ajax extends ImageBoard
{
    /**
     * Returns data of the single post
     *
     * @param int $id ID of the post
     *
     * @return string
     */
    public function post($id)
    {
        $post = $this->app->aib()->post()->get((int) $id);
        if ($post === null) {
            $this->app->abort(404, 'Post does not exists');
        }
        $post = $this->preparePost($post);
        unset($post['ip'], $post['user_agent']);
        $images = [];
        $imagesList = $this->app->aib()->image()->getList((int) $post['id'], Image::POST);
        if (isset($imagesList[$id])) {
            $images = $this->prepareImages($imagesList[$id]);
        }

        return $this->app->json(['post' => $post, 'images' => $images]);
    }

}
