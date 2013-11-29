<?php

/**
 * Part of the Trillium
 *
 * @package Trillium
 */

namespace Trillium\ImageBoard\Service;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Trillium\ImageBoard\Service\Board\Board;
use Trillium\ImageBoard\Service\Image\Image;
use Trillium\ImageBoard\Service\Post\Post;
use Trillium\ImageBoard\Service\Thread\Thread;

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
     * @var Image Image service
     */
    private $image;

    /**
     * Create Common instance
     *
     * @param \Silex\Application $app    Application instance
     * @param Board              $board  Board service
     * @param Thread             $thread Thread service
     * @param Post               $post   Post service
     * @param Image              $image  Image service
     *
     * @return Common
     */
    public function __construct(Application $app, Board $board, Thread $thread, Post $post, Image $image) {
        $this->app = $app;
        $this->board = $board;
        $this->thread = $thread;
        $this->post = $post;
        $this->image = $image;
    }

    /**
     * Create thread or post
     * Returns message form
     *
     * @param array      $board  Data of the board
     * @param array      $data   Data of the new message
     * @param array|null $thread Data of the thread for answer to the thread
     *
     * @return mixed
     */
    public function sendMessage(array $board, array $data, array $thread = null) {
        $error = [];
        if (!empty($data)) {
            if ($thread === null) {
                // Theme of the thread
                $theme = !empty($data['theme']) ? trim($data['theme']) : '';
                if (empty($theme)) {
                    $error['theme'] = $this->app->trans('The value could not be empty');
                } elseif (strlen($theme) > 200) {
                    $error['theme'] = sprintf($this->app->trans('The length of the value must not exceed %s characters'), 200);
                }
            } else {
                $sage = isset($data['sage']);
            }
            // Message
            $text = !empty($data['text']) ? trim($data['text']) : '';
            if (empty($text)) {
                $error['text'] = $this->app->trans('The value could not be empty');
            } elseif (strlen($text) > 8000) {
                $error['text'] = sprintf($this->app->trans('The length of the value must not exceed %s characters'), 8000);
            }
            // Video
            $video = !empty($data['video']) ? trim($data['video']) : '';
            if (!empty($video)) {
                $videoSubject = strtr($video, [
                    'http://www.youtube.com' => 'youtube-com',
                    'http://m.youtube.com'   => 'youtube-com',
                    'http://youtu.be/'       => 'youtube-com/watch?v=',
                ]);
                preg_match('!youtube\-com\/watch\?v=([a-z\d\-_]+)([^\s|\[]+)?!si', $videoSubject, $videoMatches);
                if (isset($videoMatches[1])) {
                    $videoSave = 'youtube.com/embed/' . $videoMatches[1];
                } else {
                    $error['video'] = $this->app->trans('Wrong video URL given');
                }
            }
            // Check images
            if (!empty($data['images']) && is_array($data['images'])) {
                $check = $this->checkImages($data['images'], $board['images_per_post'], $board['max_file_size']);
                is_string($check) ? $error['images'] = $check : $images = $check;
            }
            if (empty($error)) {
                if (isset($theme)) {
                    $threadID = $this->thread->create($board['name'], $theme);
                } else {
                    $threadID = (int) $thread['id'];
                }
                /** @var Request $request */
                $request = $this->app['request'];
                $ip = ip2long($request->getClientIp());
                $userAgent = isset($_SERVER['HTTP_USER_AGENT']) ? trim(substr($_SERVER['HTTP_USER_AGENT'], 0, 150)) : '';
                $bump = isset($sage) && $sage === true ? false : true;
                $postID = $this->post->create([
                        'board'      => $board['name'],
                        'thread'     => $threadID,
                        'text'       => $text,
                        'video'      => isset($videoSave) ? $videoSave : '',
                        'sage'       => !$bump ? 1 : 0,
                        'ip'         => $ip,
                        'user_agent' => $userAgent,
                        'time'       => time(),
                ]);
                $this->thread->bump($threadID, $thread === null ? $postID : null, $bump);
                if (!empty($images)) {
                    $this->uploadImages($images, $board['name'], $threadID, $postID, (int) $board['thumb_width']);
                }
                // Remove redudant threads
                if ($thread === null) {
                    $totalThreads = $this->thread->total($board['name']);
                    $redundantThreads = $totalThreads - $board['pages'] * $board['threads_per_page'];
                    if ($redundantThreads > 0) {
                        $redundantThreads = $this->thread->getRedundant($board['name'], $redundantThreads);
                        if (!empty($redundantThreads)) {
                            $this->post->remove($redundantThreads, 'thread');
                            $images = $this->image->getList($redundantThreads);
                            if (!empty($images)) {
                                $filePath = $this->app['imageboard.resources_path'] . $board['name'] . DS;
                                foreach ($images as $postImages) {
                                    foreach ($postImages as $image) {
                                        $imageOrig = $filePath . $image['name'] . '.' . $image['ext'];
                                        $imageThumb = $filePath . $image['name'] . '_small.' . $image['ext'];
                                        if (is_file($imageOrig)) {
                                            unlink($imageOrig);
                                        }
                                        if (is_file($imageThumb)) {
                                            unlink($imageThumb);
                                        }
                                    }
                                }
                            }
                            $this->image->remove($redundantThreads, 'thread');
                            $this->thread->remove($redundantThreads, 'id');
                        }
                    }
                }
                $this->app->redirect($this->app->url('imageboard.thread.view', ['id' => $threadID]))->send();
            }
        }
        return $this->app->view('imageboard/common/message', [
            'error' => $error,
            'theme' => isset($theme) ? $theme : '',
            'text' => isset($text) ? $text : '',
            'imagesNumber' => $board['images_per_post'],
            'newThread' => $thread === null,
        ]);
    }

    /**
     * Perfrom check images
     * Returns string, if error was occured, or array with list of the images
     *
     * @param array $images    List of the images
     * @param int   $maxImages Max number of the images
     * @param int   $maxSize   Max file size
     *
     * @return array|string
     */
    public function checkImages(array $images, $maxImages, $maxSize) {
        $tmpImages = [];
        $i = 0;
        foreach ($images as $key => $values) {
            foreach ($values as $value) {
                if (($key == 'error' && $value == 4) || empty($value)) {
                    continue;
                }
                $tmpImages[$i][$key] = $value;
                $i++;
            }
            $i = 0;
        }
        if (sizeof($tmpImages) > $maxImages) {
            $error = sprintf($this->app->trans('The number of images should be no more than %s'), $maxImages);
        } else {
            $images = [];
            foreach ($tmpImages as $image) {
                if ($image['size'] > $maxSize) {
                    $error = sprintf($this->app->trans('File size should not exceed %s'), $maxSize / 1024 . ' Kb');
                } elseif (!in_array($image['type'], ['image/png', 'image/gif', 'image/jpeg'])) {
                    $error = $this->app->trans('Illegal file type');
                } else {
                    $ext = explode('.', $image['name']);
                    $ext = strtolower(end($ext));
                    if (!in_array($ext, ['png', 'gif', 'jpg', 'jpeg'])) {
                        $error = $this->app->trans('Illegal file type');
                    }
                }
                if (!isset($error)) {
                    try {
                        $images[] = [
                            'service'  => $this->app->image($image['tmp_name']),
                            'tmp_name' => $image['tmp_name'],
                            'size' => $image['size'],
                        ];
                    } catch (\RuntimeException $e) {
                        $error = $this->app->trans($e->getMessage());
                        break;
                    }
                }
            }
        }
        return isset($error) ? $error : $images;
    }

    /**
     * Upload images and save their data to the database
     *
     * @param array  $images     List of the images
     * @param string $board      Name of the board
     * @param int    $thread     ID of the thread
     * @param int    $post       ID of the post
     * @param int    $thumbWidth Width of the thumbnail
     *
     * @return void
     */
    public function uploadImages(array $images, $board, $thread, $post, $thumbWidth) {
        $imagesData = [];
        $filePath = $this->app['imageboard.resources_path'] . $board . DS;
        foreach ($images as $image) {
            /** @var \Trillium\Image\ImageService $imageService */
            $imageService = $image['service'];
            $fileName = md5(microtime(true) . $image['tmp_name'] . rand(1000, 9999));
            if ($imageService->width() > $thumbWidth) {
                $thumb = $imageService->resizeWidth($thumbWidth);
            } else {
                $thumb = $imageService->resource();
            }
            if ($imageService->type() === IMAGETYPE_GIF) {
                copy($image['tmp_name'], $filePath . $fileName . '.gif');
            } else {
                $imageService->save($filePath . $fileName);
            }
            $imageService->save($filePath . $fileName . '_small', $thumb);
            $imagesData[] = [
                'board'    => $board,
                'thread'   => $thread,
                'post'     => $post,
                'name'     => $fileName,
                'ext'      => $imageService->extension(),
                'width'    => $imageService->width(),
                'height'   => $imageService->height(),
                'size'     => $image['size'],
            ];
        }
        $this->app->aib()->image()->insert($imagesData);
    }

}