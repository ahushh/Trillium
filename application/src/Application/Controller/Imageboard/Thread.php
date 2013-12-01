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

        $board = $this->app->aib()->board()->get($thread['board']);
        $result = $this->messageSend($board, $thread);

        $posts = '';
        $postView = $this->app->view('imageboard/post/item')->bind('post', $post)->bind('image', $postImage);
        $imageView = $this->app->view('imageboard/image/item')->bind('image', $imageData);
        $postsList = $this->app->aib()->post()->getList($id);
        $imagesList = $this->app->aib()->image()->getList($id);

        $this->app->aib()->markup()->setPosts(array_map(
            function ($post) {
                return $post['id'];
            },
            $postsList
        ));

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

        return $this->app->view('imageboard/thread/view', [
            'board'  => $boardName,
            'id'     => (int) $thread['id'],
            'theme'  => $theme,
            'posts'  => $posts,
            'answer' => $this->messageForm(false, $board['images_per_post'], is_array($result) ? $result : []),
        ]);
    }

} 