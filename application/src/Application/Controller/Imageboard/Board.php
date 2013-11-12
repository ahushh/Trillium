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
        $board = $this->app->ibBoard()->get($name);
        if ($board === null) {
            $this->app->abort(404, 'Board does not exists');
        }

        // List of the threads
        $threads = '';
        $threadsList = $this->app->ibThread()->getList($name);
        $threadView = $this->app->view('imageboard/thread/item')
            ->bind('id', $threadID)
            ->bind('theme', $threadTheme)
            ->bind('created', $threadCreated)
            ->bind('text', $threadText)
            ->bind('postID', $threadOP);
        foreach ($threadsList as $thread) {
            $threadID = (int) $thread['id'];
            $threadTheme = $this->app->escape($thread['theme']);
            $threadCreated = date('d.m.Y / H:i:s', $thread['created']);
            $threadText = nl2br($this->app->escape($thread['text']));
            $threadOP = (int) $thread['op'];
            $threads .= $threadView->render();
        }

        $board['name'] = $this->app->escape($board['name']);
        $board['summary'] = $this->app->escape($board['summary']);
        $title = '/' . $board['name'] . '/' . (!empty($board['summary']) ? ' - ' . $board['summary'] : '');
        $this->app['trillium.pageTitle'] .= ': ' . $title;
        return $this->app->view('imageboard/board/view', [
            'name' => $board['name'],
            'title' => $title,
            'messageForm' => $this->app->ibCommon()->createThread($board['name'], $_POST),
            'threads' => $threads,
        ]);
    }

}