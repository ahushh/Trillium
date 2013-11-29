<?php

/**
 * Part of the Trillium
 *
 * @package Trillium
 */

namespace Application\Controller\Panel;

use Trillium\Controller\Controller;

/**
 * Posts Class
 *
 * Posts management
 *
 * @package Application\Controller\Panel
 */
class Posts extends  Controller {

    /**
     * Remove post
     *
     * @param int $id ID of the post or thread
     *
     * @return void
     */
    public function remove($id) {
        $id = (int) $id;
        if (empty($_POST)) {
            $post = $this->app->aib()->post()->get($id);
            if ($post === null) {
                $this->app->abort(404, $this->app->trans('Post does not exists'));
            }
            $thread = (int) $post['thread'];
            $posts = $id;
        } else {
            $posts = isset($_POST['posts']) && is_array($_POST['posts']) ? array_map('intval', $_POST['posts']) : [];
            if (empty($posts)) {
                $this->app->abort(500, $this->app->trans('List of posts is empty'));
            }
            $thread = $id;
        }
        $this->app->aib()->removePost($posts);
        $this->app->redirect($this->app->url('imageboard.thread.view', ['id' => $thread]))->send();
    }

} 