<?php

/**
 * Part of the Trillium
 *
 * @package Trillium
 */

namespace Application\Controller\Imageboard;

use Application\Controller\ImageBoard;

/**
 * Thread Class
 *
 * @package Application\Controller\Imageboard
 */
class Thread extends ImageBoard {

    /**
     * Display thread
     *
     * @param int $id ID of the thread
     *
     * @return mixed
     */
    public function view($id) {
        $id = (int) $id;
        $thread = $this->app->aib()->thread()->get($id);
        if ($thread === null) {
            $this->app->abort(404, 'Thread does not exists');
        }

        $postsList = $this->app->aib()->post()->getList($id);
        $board = $this->app->aib()->board()->get($thread['board']);
        $result = $this->messageSend($board, $thread, sizeof($postsList));

        $posts = '';
        $postView = $this->app->view('imageboard/postItem')->bind('post', $post)->bind('image', $postImage);
        $imageView = $this->app->view('imageboard/imageItem')->bind('image', $imageData);
        $imagesList = $this->app->aib()->image()->getList($id);

        $this->app->aib()->markup()->setPosts($postsList);

        foreach ($postsList as $post) {
            $post = $this->preparePost($post);
            $postImage = '';
            if (array_key_exists($post['id'], $imagesList)) {
                $imagesList[$post['id']] = $this->prepareImages($imagesList[$post['id']]);
                foreach ($imagesList[$post['id']] as $imageData) {
                    $postImage .= $imageView->render();
                }
            }
            $posts .= $postView->render();
        }

        $theme = $this->app->escape($thread['theme']);
        $boardName = $this->app->escape($thread['board']);
        $this->app['trillium.pageTitle'] .= ' - /' . $boardName . '/: ' . $theme;

        return $this->app->view('imageboard/threadView', [
            'board'  => $boardName,
            'id'     => (int) $thread['id'],
            'theme'  => $theme,
            'posts'  => $posts,
            'answer' => $this->messageForm(false, $board['images_per_post'], $board['captcha'], is_array($result) ? $result : []),
        ]);
    }

}