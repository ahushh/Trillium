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
     * @param Request  $request Request
     * @param int|null $id      ID of the thread
     *
     * @return void
     */
    public function remove(Request $request, $id = null) {
        $id = !empty($_POST['threads']) && is_array($_POST['threads']) ? $_POST['threads'] : (int) $id;
        if (is_int($id)) {
            $thread = $this->app->aib()->thread()->get($id);
            if (is_null($thread)) {
                $this->app->abort(404, $this->app->trans('Thread does not exists'));
            }
        }
        $this->app->aib()->removeThread($id);
        $url = isset($thread)
            ? $this->app->url('imageboard.board.view', ['name' => $thread['board']])
            : $request->headers->get('Referer', 'http://' . $_SERVER['SERVER_NAME']);
        $this->app->redirect($url)->send();
    }

} 