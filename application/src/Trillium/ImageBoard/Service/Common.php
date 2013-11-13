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
            // TODO: upload images
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
            if (!empty($data['images']) && is_array($data['images'])) {
                $tmpImages = [];
                $i = 0;
                foreach ($data['images'] as $key => $values) {
                    foreach ($values as $value) {
                        if (($key == 'error' && $value == 4) || empty($value)) {
                            continue;
                        }
                        $tmpImages[$i][$key] = $value;
                        $i++;
                    }
                    $i = 0;
                }
                $images = [];
                foreach ($tmpImages as $image) {
                    if ($image['size'] > 500000) { // TODO: setup size
                        $error['images'] = sprintf($this->app->trans('File size should not exceed %s'), 500000 . ' bytes');
                    } elseif (!in_array($image['type'], ['image/png', 'image/gif', 'image/jpeg'])) {
                        $error['images'] = $this->app->trans('Illegal file type');
                    } else {
                        $ext = explode('.', $image['name']);
                        $ext = strtolower(end($ext));
                        if (!in_array($ext, ['png', 'gif', 'jpg', 'jpeg'])) {
                            $error['images'] = $this->app->trans('Illegal file type');
                        }
                    }
                    if (!isset($error['images'])) {
                        try {
                            $images[] = [
                                'service'  => $this->app->image($image['tmp_name']),
                                'tmp_name' => $image['tmp_name'],
                                'size' => $image['size'],
                            ];
                        } catch (\RuntimeException $e) {
                            $error['images'] = $this->app->trans($e->getMessage());
                            break;
                        }
                    }
                }
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
                // Upload images
                if (!empty($images)) {
                    $imagesData = [];
                    $filePath = $this->app['imageboard.resources_path'] . $thread['board'] . DS;
                    foreach ($images as $image) {
                        /** @var \Trillium\Image\ImageService $imageService */
                        $imageService = $image['service'];
                        $fileName = md5(microtime(true) . $image['tmp_name'] . rand(1000, 9999));
                        $thumb = $imageService->resizeWidth(200); // TODO: setup width
                        if ($imageService->type() === IMAGETYPE_GIF) {
                            copy($image['tmp_name'], $filePath . $fileName . '.gif');
                        } else {
                            $imageService->save($filePath . $fileName);
                        }
                        $imageService->save($filePath . $fileName . '_small', $thumb);
                        $imagesData[] = [
                            'board'    => $thread['board'],
                            'thread'   => (int) $thread['id'],
                            'post'     => $postID,
                            'name'     => $fileName,
                            'ext'      => $imageService->extension(),
                            'width'    => $imageService->width(),
                            'height'   => $imageService->height(),
                            'size'     => $image['size'],
                        ];
                    }
                    $this->app->ibImage()->insert($imagesData);
                }
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