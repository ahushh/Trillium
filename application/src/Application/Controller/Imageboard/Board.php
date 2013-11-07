<?php

/**
 * Part of the Trillium
 *
 * @package Trillium
 */

namespace Application\Controller\Imageboard;

use Trillium\Controller\Controller;

/**
 * Board Class
 *
 * @package Application\Controller\Imageboard
 */
class Board extends Controller {

    /**
     * Display board
     *
     * @param $name
     *
     * @return mixed
     */
    public function view($name) {
        $board = $this->app['model']('Boards')->get($name);
        if ($board === null) {
            $this->app->abort(404, 'Board does not exists');
        }
        $board['name'] = $this->app->escape($board['name']);
        $board['summary'] = $this->app->escape($board['summary']);
        $title = '/' . $board['name'] . '/' . (!empty($board['summary']) ? ' - ' . $board['summary'] : '');
        $this->app['trillium.pageTitle'] .= ': ' . $title;
        return $this->app->view('imageboard/board/view', [
            'name' => $board['name'],
            'title' => $title,
        ]);
    }

}