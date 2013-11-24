<?php

/**
 * Part of the Trillium
 *
 * @package Trillium
 */

namespace Application\Controller\Panel;

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
            function ($images) use ($thread) {
                foreach ($images as $image) {
                    $image = $this->app['imageboard.resources_path'] . $thread['board'] . DS . $image['name'] . '%s.' . $image['ext'];
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

} 