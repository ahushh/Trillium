<?php

/**
 * Part of the Trillium
 *
 * @package Trillium
 */

namespace Application\Controller\Panel;

use Symfony\Component\HttpFoundation\Request;
use Trillium\Controller\Controller;

/**
 * Threads Class
 *
 * Threads managament
 *
 * @package Application\Controller\Panel
 */
class Threads extends Controller {

    /**
     * Remove thread
     *
     * @param int $id ID of the thread
     *
     * @return void
     */
    public function remove($id) {
        $id = (int) $id;
        $thread = $this->app->ibThread()->get($id);
        if ($thread === null) {
            $this->app->abort(404, $this->app->trans('Thread is not exists'));
        }
        $this->app->ibThread()->remove($id, 'id');
        $this->app->ibPost()->remove($id, 'thread');
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
            $this->app->ibImage()->getList($id, 'thread')
        );
        $this->app->ibImage()->remove($id, 'thread');
        $this->app->redirect($this->app->url('imageboard.board.view', ['name' => $thread['board']]))->send();
    }

    /**
     * Mass remove
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return void
     */
    public function massRemove(Request $request) {
        $threads = isset($_POST['threads']) && is_array($_POST['threads']) ? array_map('intval', $_POST['threads']) : [];
        if (empty($threads)) {
            $this->app->abort(500, $this->app->trans('Threads list is empty'));
        }
        $this->app->ibThread()->remove($threads, 'id');
        $this->app->ibPost()->remove($threads, 'thread');
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
            $this->app->ibImage()->getList($threads, 'thread')
        );
        $this->app->ibImage()->remove($threads, 'thread');
        $this->app->redirect($request->headers->get('Referer', 'http://' . $_SERVER['SERVER_NAME']))->send();
    }

} 