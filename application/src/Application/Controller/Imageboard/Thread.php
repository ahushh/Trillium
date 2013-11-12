<?php

/**
 * Part of the Trillium
 *
 * @package Trillium
 */

namespace Application\Controller\Imageboard;

use Trillium\Controller\Controller;

/**
 * Thread Class
 *
 * @package Application\Controller\Imageboard
 */
class Thread extends Controller {

    /**
     * Display thread
     *
     * @param int $id ID of the thread
     *
     * @return mixed
     */
    public function view($id) {
        $id = (int) $id;
        $thread = $this->app->ibThread()->get($id);
        if ($thread === null) {
            $this->app->abort(404, $this->app->trans('Thread does not exists'));
        }
        $theme = $this->app->escape($thread['theme']);
        $posts = '';
        $postView = $this->app->view('imageboard/post/item')
            ->bind('id', $postID)
            ->bind('text', $postText)
            ->bind('time', $postTime);
        $postsList = $this->app->ibPost()->getList($id);
        foreach ($postsList as $post) {
            $postID = (int) $post['id'];
            $postText = nl2br($this->app->escape($post['text']));
            $postTime = date('d.m.Y / H:i:s', $post['time']);
            $posts .= $postView->render();
        }
        return $this->app->view('imageboard/thread/view', [
            'theme' => $theme,
            'posts' => $posts,
        ]);
    }

} 