<?php

/**
 * Part of the Trillium
 *
 * @package Trillium
 */

namespace Application\Controller\Imageboard;

use Application\Controller\ImageBoard;

/**
 * Board Class
 *
 * @package Application\Controller\Imageboard
 */
class Board extends ImageBoard {

    /**
     * Display board
     *
     * @param string $name Name of the board
     * @param int    $page Number of the current page
     *
     * @return mixed
     */
    public function view($name, $page = 1) {
        $board = $this->app->aib()->board()->get($name);
        if ($board === null) {
            $this->app->abort(404, 'Board does not exists');
        }

        $result = $this->messageSend($board);

        $threads      = '';
        $totalThreads = $this->app->aib()->thread()->total($name);
        if ($totalThreads > 0) {
            $pagination  = $this->app->pagination($totalThreads, $page, $this->app->url('imageboard.board.view', ['name' => $name]) . '/', 5);
            $threadView  = $this->app->view('imageboard/threadItem')->bind('thread', $thread);
            foreach ($this->app->aib()->thread()->getList($name, $pagination->offset(), $pagination->limit()) as $thread) {
                $thread   = $this->prepareThread($thread);
                $threads .= $threadView->render();
            }
        }

        $board['name']    = $this->app->escape($board['name']);
        $board['summary'] = $this->app->escape($board['summary']);
        $title            = '/' . $board['name'] . '/' . (!empty($board['summary']) ? ' - ' . $board['summary'] : '');
        $this->app['trillium.pageTitle'] .= ': ' . $title;
        $captcha = $board['captcha'] && $this->app->user() === null;

        return $this->app->view('imageboard/boardView', [
            'name'        => $board['name'],
            'title'       => $title,
            'messageForm' => $this->messageForm(true, $board['images_per_post'], $captcha, is_array($result) ? $result : []),
            'threads'     => $threads,
            'pagination'  => isset($pagination) ? $pagination->view() : '',
        ]);
    }

}