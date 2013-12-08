<?php

/**
 * Part of the Trillium
 *
 * @package Trillium
 */

namespace Application\Controller\Imageboard;

use Application\Controller\ImageBoard;

/**
 * Thread Class
 *
 * @package Application\Controller\Imageboard
 */
class Thread extends ImageBoard {

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
            $this->app->abort(404, 'Thread does not exists');
        }

        $postsList = $this->app->aib()->post()->getList($id);
        $board = $this->app->aib()->board()->get($thread['board']);
        if ($thread['close'] == 0) {
            $result = $this->messageSend($board, $thread, sizeof($postsList));
        }

        $posts = '';
        $postView = $this->app->view('imageboard/postItem')->bind('post', $post)->bind('image', $postImage);
        $imageView = $this->app->view('imageboard/imageItem')->bind('image', $imageData);
        $imagesList = $this->app->aib()->image()->getList($id);

        $this->app->aib()->markup()->setPosts($postsList);

        foreach ($postsList as $post) {
            $post = $this->preparePost($post);
            $postImage = '';
            if (array_key_exists($post['id'], $imagesList)) {
                $imagesList[$post['id']] = $this->prepareImages($imagesList[$post['id']]);
                foreach ($imagesList[$post['id']] as $imageData) {
                    $postImage .= $imageView->render();
                }
            }
            $posts .= $postView->render();
        }

        $thread['theme'] = $this->app->escape($thread['theme']);
        $boardName = $this->app->escape($thread['board']);
        $this->app['trillium.pageTitle'] .= ' - /' . $boardName . '/: ' . $thread['theme'];
        $captcha = $board['captcha'] && $this->app->user() === null;
        $beforeBumpLimit = $board['bump_limit'] == 0 ? null : $board['bump_limit'] - sizeof($postsList);

        // Manage menu
        /** @var \Symfony\Component\Security\Core\SecurityContext $security */
        $security = $this->app['security'];
        if ($security->isGranted('ROLE_ADMIN')) {
            $manageMenu = [];
            $manageParams = [
                ['panel.imageboard.thread.remove', ['id' => $id], 'Remove'],
                ['panel.imageboard.thread.rename', ['id' => $id], 'Rename'],
                ['panel.imageboard.thread.move',   ['id' => $id], 'Move'],
                ['panel.imageboard.thread.manage', ['id' => $id, 'action' => 'autosage'], 'Autosage'],
                ['panel.imageboard.thread.manage', ['id' => $id, 'action' => 'autobump'], 'Autobump'],
                ['panel.imageboard.thread.manage', ['id' => $id, 'action' => 'attach'], 'Attach'],
                ['panel.imageboard.thread.manage', ['id' => $id, 'action' => 'close'], ($thread['close'] == 0 ? 'Close' : 'Open')],
            ];
            foreach ($manageParams as $manageParam) {
                $manageMenu[] = [
                    'title' => $this->app->trans($manageParam[2]),
                    'url' => $this->app->url($manageParam[0], $manageParam[1]),
                ];
            }
        }

        return $this->app->view('imageboard/threadView', [
            'manageMenu'      => isset($manageMenu) ? $manageMenu : null,
            'thread'          => $thread,
            'posts'           => $posts,
            'beforeBumpLimit' => $beforeBumpLimit === null ? null : ($beforeBumpLimit > 0 ? $beforeBumpLimit : 0),
            'answer'          => $thread['close'] == 1 ? '' : $this->messageForm(
                false,
                $board['images_per_post'],
                $board['max_file_size'],
                $board['blotter'],
                $captcha,
                isset($result) && is_array($result) ? $result : []
            ),
        ]);
    }

}