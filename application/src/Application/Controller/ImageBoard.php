<?php

/**
 * Part of the Trillium
 *
 * @package Trillium
 */

namespace Application\Controller;

use Trillium\Controller\Controller;

/**
 * ImageBoard Class
 *
 * Base imageboard class
 *
 * @package Application\Controller
 */
class ImageBoard extends Controller {

    /**
     * Prepare list of the images to display
     *
     * @param array $images List of the images
     *
     * @return array
     */
    public final function prepareImages(array $images) {
        $result = [];
        foreach ($images as $image) {
            $imageBaseURL = 'http://' . $_SERVER['SERVER_NAME'] . '/assets/boards/' . $image['board'] . '/' . $image['name'];
            $result[] = [
                'id'         => (int) $image['id'],
                'original'   => $imageBaseURL . '.' . $image['ext'],
                'thumbnail'  => $imageBaseURL . '_small.' . $image['ext'],
                'resolution' => $image['width'] . 'x' . $image['height'] . ' px',
                'size'       => round($image['size'] / 1024) . ' KiB',
                'type'       => strtoupper($image['ext']),
            ];
        }
        return $result;
    }

    /**
     * Prepare post to display
     *
     * @param array $post Data of the post
     *
     * @return array
     */
    public final function preparePost(array $post) {
        return [
            'id'         => (int) $post['id'],
            'text'       => $this->app->aib()->markup()->handle($post['text'], (int) $post['id']),
            'time'       => $this->formatDate((int) $post['time']),
            'sage'       => (int) $post['sage'],
            'ip'         => long2ip($post['ip']),
            'user_agent' => $this->app->escape($post['user_agent']),
            'video'      => !empty($post['video'])
                ? [
                    'source' => 'http://' . $post['video'],
                    'image'  => 'http://' . str_replace('youtube.com/embed/', 'img.youtube.com/vi/', $post['video']) . '/1.jpg',
                ]
                : false
        ];
    }

    /**
     * Prepare thread to display
     *
     * @param array $thread Data of the thread
     *
     * @return array
     */
    public final function prepareThread(array $thread) {
        return [
            'id'      => (int) $thread['id'],
            'theme'   => $this->app->escape($thread['theme']),
            'created' => $this->formatDate((int) $thread['created']),
            'op'      => (int) $thread['op'],
            'text'    => $this->app->aib()->markup()->handle(mb_substr($thread['text'], 0, 100))
                       . (mb_strlen($thread['text']) > 100 ? '&hellip;' : ''),
        ];
    }

    /**
     * Send message
     * If thread is array - answer, else create new thread
     * Returns array, if errors occured
     * Returns Response, if message created
     * Returns null, if data is empty
     *
     * @param array      $board  Data of the board
     * @param array|null $thread Data of the thread
     *
     * @return array|\Symfony\Component\HttpFoundation\Response|null
     */
    public final function messageSend(array $board, array $thread = null) {
        if (!empty($_POST)) {
            /** @var $request \Symfony\Component\HttpFoundation\Request */
            $request = $this->app['request'];
            $ip = ip2long($request->getClientIp());
            $result = $this->app->aibMessage()->send($board, array_merge($_POST, $_FILES), $ip, $thread);
            if (is_int($result)) {
                return $this->app->redirect($this->app->url('imageboard.thread.view', ['id' => $result]))->send();
            } else {
                return array_map(
                    function ($item) {
                        if (is_array($item)) {
                            $item[0] = $this->app->trans($item[0]);
                            $item = call_user_func_array('sprintf', $item);
                        } else {
                            $item = $this->app->trans($item);
                        }
                        return $item;
                    },
                    $result
                );
            }
        }
        return null;
    }

    /**
     * Prepare message form to display
     *
     * @param boolean $newThread    New thread or answer to thread
     * @param int     $imagesNumber Number of the images to attach
     * @param array   $error        Error messages
     *
     * @return string
     */
    public final function messageForm($newThread, $imagesNumber, array $error = []) {
       return (string) $this->app->view('imageboard/common/message', [
           'error'        => $error,
           'theme'        => $newThread ? (isset($_POST['theme']) ? trim($_POST['theme']) : '') : '',
           'text'         => isset($_POST['text']) ? trim($_POST['text']) : '',
           'imagesNumber' => $imagesNumber,
           'newThread'    => $newThread,
       ]);
    }

    /**
     * Format date
     * @todo Timeshift
     *
     * @param int $time Timestamp
     *
     * @return boolean|string
     */
    public final function formatDate($time) {
        return date('d.m.Y / H:i:s', $time);
    }

}