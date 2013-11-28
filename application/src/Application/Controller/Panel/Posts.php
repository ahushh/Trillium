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
     * @param int $id ID of the post
     *
     * @return void
     */
    public function remove($id) {
        $id = (int) $id;
        $post = $this->app->ibPost()->get($id);
        if ($post === null) {
            $this->app->abort(404, $this->app->trans('Post does not exists'));
        }
        $this->app->ibPost()->remove($id, 'id');
        array_map(
            function ($images) {
                foreach ($images as $image) {
                    $image = $this->app['imageboard.resources_path'] . $image['board'] . DS . $image['name'] . '%s.' . $image['ext'];
                    if (is_file(sprintf($image, ''))) {
                        unlink(sprintf($image, ''));
                    }
                    if (is_file(sprintf($image, '_small'))) {
                        unlink(sprintf($image, '_small'));
                    }
                }
            },
            $this->app->ibImage()->getList($id, 'post')
        );
        $this->app->ibImage()->remove($id, 'post');
        $this->app->redirect($this->app->url('imageboard.thread.view', ['id' => (int) $post['thread']]))->send();
    }

    /**
     * Mass remove
     *
     * @param int $id ID of the thread
     *
     * @return void
     */
    public function massRemove($id) {
        $id = (int) $id;
        $posts = isset($_POST['posts']) && is_array($_POST['posts']) ? array_map('intval', $_POST['posts']) : [];
        if (empty($posts)) {
            $this->app->abort(500, $this->app->trans('List of posts is empty'));
        }
        $this->app->ibPost()->remove($posts, 'id');
        array_map(
            function ($images) {
                foreach ($images as $image) {
                    $image = $this->app['imageboard.resources_path'] . $image['board'] . DS . $image['name'] . '%s.' . $image['ext'];
                    if (is_file(sprintf($image, ''))) {
                        unlink(sprintf($image, ''));
                    }
                    if (is_file(sprintf($image, '_small'))) {
                        unlink(sprintf($image, '_small'));
                    }
                }
            },
            $this->app->ibImage()->getList($posts, 'post')
        );
        $this->app->ibImage()->remove($posts, 'post');
        $this->app->redirect($this->app->url('imageboard.thread.view', ['id' => $id]))->send();
    }

} 