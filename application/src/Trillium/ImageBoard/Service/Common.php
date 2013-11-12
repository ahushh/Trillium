<?php

/**
 * Part of the Trillium
 *
 * @package Trillium
 */

namespace Trillium\ImageBoard\Service;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

/**
 * Common Class
 *
 * @package Trillium\ImageBoard\Service
 */
class Common {

    /**
     * @var \Trillium\Silex\Application Application instance
     */
    private $app;

    /**
     * @var Board Board service
     */
    private $board;

    /**
     * @var Thread Thread service
     */
    private $thread;

    /**
     * @var Post Post service
     */
    private $post;

    /**
     * Create Common instance
     *
     * @param \Silex\Application $app    Application instance
     *
     * @param Board              $board  Board service
     * @param Thread             $thread Thread service
     * @param Post               $post   Post service
     *
     * @return Common
     */
    public function __construct(Application $app, Board $board, Thread $thread, Post $post) {
        $this->app = $app;
        $this->board = $board;
        $this->thread = $thread;
        $this->post = $post;
    }

    /**
     * Create thread
     *
     * @param string $board Name of the board
     * @param array  $data  Data
     *
     * @return mixed
     * @todo Refactoring
     */
    public function createThread($board, array $data) {
        $error = [];
        if (!empty($data)) {
            $theme = !empty($data['theme']) ? trim($data['theme']) : '';
            $text = !empty($data['text']) ? trim($data['text']) : '';
            if (empty($theme)) {
                $error['theme'] = $this->app->trans('The value could not be empty');
            } elseif (strlen($theme) > 200) {
                $error['theme'] = sprintf($this->app->trans('The length of the value must not exceed %s characters'), 200);
            }
            if (empty($text)) {
                $error['text'] = $this->app->trans('The value could not be empty');
            } elseif (strlen($text) > 8000) {
                $error['text'] = sprintf($this->app->trans('The length of the value must not exceed %s characters'), 8000);
            }
            if (empty($error)) {
                /**
                 * @var Request $request
                 */
                $request = $this->app['request'];
                $ip = ip2long($request->getClientIp());
                $userAgent = isset($_SERVER['HTTP_USER_AGENT']) ? trim(substr($_SERVER['HTTP_USER_AGENT'], 0, 150)) : '';
                $threadID = $this->thread->create($board, $theme);
                $postID = $this->post->create($board, $threadID, $text, $ip, $userAgent);
                $this->thread->bump($threadID, $postID);
                $this->app->redirect($this->app->url('imaegboard.thread.view', ['id' => $threadID]))->send();
            }
        }
        return $this->app->view('imageboard/common/message', [
            'error' => $error,
            'theme' => isset($theme) ? $theme : '',
            'text' => isset($text) ? $text : '',
            'formAction' => '',
            'title' => $this->app->trans('Create thread'),
        ]);
    }

    /**
     * Answer to the thread
     *
     * @param array $thread ID of the thread
     * @param array $data   Data
     *
     * @return mixed
     * @todo Refactoring
     */
    public function createPost(array $thread, array $data) {
        $error = [];
        if (!empty($data)) {
            $text = !empty($data['text']) ? trim($data['text']) : '';
            if (empty($text)) {
                $error['text'] = $this->app->trans('The value could not be empty');
            } elseif (strlen($text) > 8000) {
                $error['text'] = sprintf($this->app->trans('The length of the value must not exceed %s characters'), 8000);
            }
            if (empty($error)) {
                /**
                 * @var Request $request
                 */
                $request = $this->app['request'];
                $ip = ip2long($request->getClientIp());
                $userAgent = isset($_SERVER['HTTP_USER_AGENT']) ? trim(substr($_SERVER['HTTP_USER_AGENT'], 0, 150)) : '';
                $postID = $this->post->create($thread['board'], (int) $thread['id'], $text, $ip, $userAgent);
                $this->thread->bump((int) $thread['id'], $postID);
                $this->app->redirect($this->app->url('imageboard.thread.view', ['id' => $thread['id']]))->send();
            }
        }
        return $this->app->view('imageboard/common/message', [
            'error' => $error,
            'text' => isset($text) ? $text : '',
            'formAction' => '',
            'title' => $this->app->trans('Answer'),
        ]);
    }

}