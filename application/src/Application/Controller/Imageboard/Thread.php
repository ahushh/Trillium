<?php

/**
 * Part of the Trillium
 *
 * @package Trillium
 */

namespace Application\Controller\Imageboard;

use Trillium\Controller\Controller;

/**
 * Thread Class
 *
 * @package Application\Controller\Imageboard
 */
class Thread extends Controller {

    /**
     * Display thread
     *
     * @param int $id ID of the thread
     *
     * @return mixed
     */
    public function view($id) {
        $id = (int) $id;
        $thread = $this->app->aib()->thread()->get($id);
        if ($thread === null) {
            $this->app->abort(404, $this->app->trans('Thread does not exists'));
        }
        $board = $this->app->aib()->board()->get($thread['board']);
        if (!empty($_POST)) {
            /** @var $request \Symfony\Component\HttpFoundation\Request */
            $request = $this->app['request'];
            $ip = ip2long($request->getClientIp());
            $result = $this->app->aibMessage()->send($board, array_merge($_POST, $_FILES), $ip, $thread);
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

        $posts = '';
        $postView = $this->app->view('imageboard/post/item')
            ->bind('id', $postID)
            ->bind('text', $postText)
            ->bind('time', $postTime)
            ->bind('image', $postImage)
            ->bind('sage', $postSage)
            ->bind('video', $postVideo);
        $imageView = $this->app->view('imageboard/image/item')
            ->bind('original', $imageOriginal)
            ->bind('thumbnail', $imageThumbnail)
            ->bind('resolution', $imageResolution)
            ->bind('size', $imageSize)
            ->bind('type', $imageType)
            ->bind('id', $imageID);
        $postsList = $this->app->aib()->post()->getList($id);
        $imagesList = $this->app->aib()->image()->getList($id);

        $this->app->aib()->markup()->setPosts(array_map(
            function ($post) {
                return $post['id'];
            },
            $postsList
        ));

        foreach ($postsList as $post) {
            $postID = (int) $post['id'];
            $postText = $this->app->aib()->markup()->handle($post['text'], $postID);
            $postTime = date('d.m.Y / H:i:s', $post['time']);
            $postSage = (int) $post['sage'];
            $postVideo = !empty($post['video'])
                ? [
                    'source' => 'http://' . $post['video'],
                    'image' => 'http://' . str_replace('youtube.com/embed/', 'img.youtube.com/vi/', $post['video']) . '/1.jpg',
                ]
                : null;
            $postImage = '';
            if (array_key_exists($postID, $imagesList)) {
                foreach ($imagesList[$postID] as $image) {
                    $imageID = (int) $image['id'];
                    $imageBaseURL = 'http://' . $_SERVER['SERVER_NAME'] . '/assets/boards/' . $thread['board'] . '/' . $image['name'];
                    $imageOriginal = $imageBaseURL . '.' . $image['ext'];
                    $imageThumbnail = $imageBaseURL . '_small.' .$image['ext'];
                    $imageResolution = $image['width'] . 'x' . $image['height'] . ' px';
                    $imageSize = round($image['size'] / 1024) . ' KiB';
                    $imageType = strtoupper($image['ext']);
                    $postImage .= $imageView->render();
                }
            }
            $posts .= $postView->render();
        }
        $theme = $this->app->escape($thread['theme']);
        $boardName = $this->app->escape($thread['board']);
        $this->app['trillium.pageTitle'] .= ' - /' . $boardName . '/: ' . $theme;
        return $this->app->view('imageboard/thread/view', [
            'board'  => $boardName,
            'id'     => (int) $thread['id'],
            'theme'  => $theme,
            'posts'  => $posts,
            'answer' => $this->app->view('imageboard/common/message', [
                'error'        => isset($error) ? $error : [],
                'text'         => isset($_POST['text']) ? $_POST['text'] : '',
                'imagesNumber' => $board['images_per_post'],
                'newThread'    => false,
            ]),
        ]);
    }

} 