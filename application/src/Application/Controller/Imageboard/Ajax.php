<?php

/**
 * Part of the Trillium
 *
 * @package Trillium
 */

namespace Application\Controller\Imageboard;

use Trillium\Controller\Controller;

/**
 * Ajax Class
 *
 * @package Application\Controller\Imageboard
 */
class Ajax extends Controller {

    /**
     * Returns data of the single post
     *
     * @param int $id ID of the post
     *
     * @return string
     */
    public function post($id) {
        $post = $this->app->ibPost()->get((int) $id);
        if ($post === null) {
            $this->app->abort(404, $this->app->trans('The post is not exists'));
        }
        $post['text'] = $this->app->ibMarkup()->handle($post['text']);
        $post['time'] = date('d.m.Y / H:i:s', $post['time']);
        $post['sage'] = (int) $post['sage'];
        unset($post['ip'], $post['user_agent']);

        $images = [];
        $imagesList = $this->app->ibImage()->getList((int) $post['id'], 'post');
        if (isset($imagesList[$id])) {
            $i = 0;
            foreach ($imagesList[$id] as $image) {
                $imageBaseURL = 'http://' . $_SERVER['SERVER_NAME'] . '/assets/boards/' . $image['board'] . '/' . $image['name'];
                $images[$i]['original'] = $imageBaseURL . '.' . $image['ext'];
                $images[$i]['thumbnail'] = $imageBaseURL . '_small.' . $image['ext'];
                $images[$i]['resolution'] = $image['width'] . 'x' . $image['height'] . ' px';
                $images[$i]['size'] = round($image['size'] / 1024) . ' KiB';
                $images[$i]['type'] = strtoupper($image['ext']);
                $i++;
            }
        }

        return $this->app->json(['post' => $post, 'images' => $images]);
    }

} 