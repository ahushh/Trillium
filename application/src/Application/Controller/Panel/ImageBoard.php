<?php

/**
 * Part of the Trillium
 *
 * @package Trillium
 */

namespace Application\Controller\Panel;

use Symfony\Component\HttpFoundation\Request;
use Trillium\Controller\Controller;

/**
 * ImageBoard Class
 *
 * @package Application\Controller\Panel
 */
class ImageBoard extends Controller {

    /**
     * List of the boards
     *
     * @return mixed
     */
    public function boardList() {
        $output = '';
        $list = $this->app->aib()->board()->getList();
        if (!empty($list)) {
            $itemWrapper = $this->app->view('panel/imageboard/boardListItem')->bind('name', $boardName)->bind('summary', $boardSummary);
            foreach ($list as $board) {
                $boardName = $this->app->escape($board['name']);
                $boardSummary = $this->app->escape($board['summary']);
                $output.= $itemWrapper->render();
            }
        }
        return $this->app->view('panel/imageboard/boardList', ['list' => $output]);
    }

    /**
     * Create or update board
     *
     * @param string $name Name of the board
     *
     * @return mixed
     */
    public function boardManage($name = '') {
        if ($name !== '') {
            $data = $this->app->aib()->board()->get($name);
            if ($data === null) {
                $this->app->abort(404, 'Board does not exists');
            }
            $data['max_file_size'] = $data['max_file_size'] / 1024;
        }
        if (!empty($_POST)) {
            $result = $this->app->aib()->board()->manage($_POST, isset($data) ? $data : []);
            if ($result === true) {
                $this->app->redirect($this->app->url('panel.imageboard.board.list'))->send();
            } else {
                $data = $result['data'];
                if (!isset($data['name'])) {
                    $data['name'] = $name;
                }
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
        return $this->app->view('panel/imageboard/boardManage', [
            'error' => isset($error) ? $error : [],
            'data'  => isset($data) ? $data : [],
            'edit'  => $name !== '',
        ]);
    }

    /**
     * Remove board
     *
     * @param string $name Name of the board
     *
     * @return void
     */
    public function boardRemove($name) {
        $this->app->aib()->removeBoard($name);
        $this->app->redirect($this->app->url('panel.imageboard.board.list'))->send();
    }

    /**
     * Remove image
     *
     * @param int $id ID of the image
     *
     * @return void
     */
    public function imageRemove($id) {
        $id = (int) $id;
        $image = $this->app->aib()->image()->get($id);
        if ($image === null) {
            $this->app->abort(404, $this->app->trans('Image is not exists'));
        }
        $this->app->aib()->image()->removeFiles([[$image]]);
        $this->app->aib()->image()->remove($id, 'id');
        $this->app->redirect($this->app->url('imageboard.thread.view', ['id' => $image['thread']]))->send();
    }

    /**
     * Remove post
     *
     * @param int $id ID of the post or thread
     *
     * @return void
     */
    public function postRemove($id) {
        $id = (int) $id;
        if (empty($_POST)) {
            $post = $this->app->aib()->post()->get($id);
            if ($post === null) {
                $this->app->abort(404, $this->app->trans('Post does not exists'));
            }
            $thread = (int) $post['thread'];
            $posts = $id;
        } else {
            $posts = isset($_POST['posts']) && is_array($_POST['posts']) ? array_map('intval', $_POST['posts']) : [];
            if (empty($posts)) {
                $this->app->abort(500, $this->app->trans('List of posts is empty'));
            }
            $thread = $id;
        }
        $this->app->aib()->removePost($posts);
        $this->app->redirect($this->app->url('imageboard.thread.view', ['id' => $thread]))->send();
    }

    /**
     * Remove thread
     *
     * @param Request  $request Request
     * @param int|null $id      ID of the thread
     *
     * @return void
     */
    public function threadRemove(Request $request, $id = null) {
        $id = !empty($_POST['threads']) && is_array($_POST['threads']) ? $_POST['threads'] : (int) $id;
        if (is_int($id)) {
            $thread = $this->app->aib()->thread()->get($id);
            if (is_null($thread)) {
                $this->app->abort(404, $this->app->trans('Thread does not exists'));
            }
        }
        $this->app->aib()->removeThread($id);
        $url = isset($thread)
            ? $this->app->url('imageboard.board.view', ['name' => $thread['board']])
            : $request->headers->get('Referer', 'http://' . $_SERVER['SERVER_NAME']);
        $this->app->redirect($url)->send();
    }

} 