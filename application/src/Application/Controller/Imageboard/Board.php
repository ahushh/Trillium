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

        // Send message
        if (!empty($_POST)) {
            /** @var $request \Symfony\Component\HttpFoundation\Request */
            $request = $this->app['request'];
            $ip = ip2long($request->getClientIp());
            $result = $this->app->aibMessage()->send($board, array_merge($_POST, $_FILES), $ip);
            if (is_int($result)) {
                $this->app->redirect($this->app->url('imageboard.thread.view', ['id' => $result]))->send();
            } else {
                $error = array_map(
                    function ($item) {
                        if (is_array($item)) {
                            $item[0] = $this->app->trans($item[0]);
                            $item = call_user_func_array('sprintf', $item);
                        } else {
                            $item = $this->app->trans($item);
                        }
                        return $item;
                    },
                    $result['error']
                );
            }
        }

        // List of the threads
        $threads      = '';
        $totalThreads = $this->app->aib()->thread()->total($name);
        if ($totalThreads > 0) {
            $pagination  = $this->app->pagination($totalThreads, $page, $this->app->url('imageboard.board.view', ['name' => $name]) . '/', 5);
            $threadsList = $this->app->aib()->thread()->getList($name, $pagination->offset(), $pagination->limit());
            $threadView  = $this->app->view('imageboard/thread/item')
                ->bind('id', $threadID)
                ->bind('theme', $threadTheme)
                ->bind('created', $threadCreated)
                ->bind('text', $threadText)
                ->bind('postID', $threadOP);
            foreach ($threadsList as $thread) {
                $threadID      = (int) $thread['id'];
                $threadTheme   = $this->app->escape($thread['theme']);
                $threadCreated = date('d.m.Y / H:i:s', $thread['created']);
                $threadOP      = (int) $thread['op'];
                $threadText    = $this->app->aib()->markup()->handle(mb_substr($thread['text'], 0, 100))
                               . (mb_strlen($thread['text']) > 100 ? '&hellip;' : '');
                $threads       .= $threadView->render();
            }
        }

        $board['name']    = $this->app->escape($board['name']);
        $board['summary'] = $this->app->escape($board['summary']);
        $title            = '/' . $board['name'] . '/' . (!empty($board['summary']) ? ' - ' . $board['summary'] : '');
        $this->app['trillium.pageTitle'] .= ': ' . $title;
        return $this->app->view('imageboard/board/view', [
            'name'        => $board['name'],
            'title'       => $title,
            'messageForm' => $this->app->view('imageboard/common/message', [
                'error'        => isset($error) ? $error : [],
                'theme'        => isset($_POST['theme']) ? $_POST['theme'] : '',
                'text'         => isset($_POST['text']) ? $_POST['text'] : '',
                'imagesNumber' => $board['images_per_post'],
                'newThread'    => true,
            ]),
            'threads'     => $threads,
            'pagination'  => isset($pagination) ? $pagination->view() : '',
        ]);
    }

}