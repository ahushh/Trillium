<?php

/**
 * Part of the Trillium
 *
 * @package Trillium
 */

namespace Application\Controller\Panel;

use Symfony\Component\HttpFoundation\Request;
use Trillium\Controller\Controller;
use Trillium\ImageBoard\Service\Image\Image;

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
            $itemWrapper = $this->app->view('panel/imageboard/boardListItem')->bind('board', $board);
            foreach ($list as $board) {
                $board['name']    = $this->app->escape($board['name']);
                $board['summary'] = $this->app->escape($board['summary']);
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
            $this->app->abort(404, 'Image is not exists');
        }
        $this->app->aib()->image()->removeFiles([[$image]]);
        $this->app->aib()->image()->remove($id, Image::ID);
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
                $this->app->abort(404, 'Post does not exists');
            }
            $thread = (int) $post['thread'];
            $posts = $id;
        } else {
            $posts = isset($_POST['posts']) && is_array($_POST['posts']) ? array_map('intval', $_POST['posts']) : [];
            if (empty($posts)) {
                $this->app->abort(500, 'List of posts is empty');
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
            $thread = $this->threadFind($id);
        }
        $this->app->aib()->removeThread($id);
        $url = isset($thread)
            ? $this->app->url('imageboard.board.view', ['name' => $thread['board']])
            : $request->headers->get('Referer', 'http://' . $_SERVER['SERVER_NAME']);
        $this->app->redirect($url)->send();
    }

    /**
     * Manage thread
     *
     * @param string $action Name of the action
     * @param int    $id     ID of the thread
     *
     * @return void
     */
    public function threadManage($action, $id) {
        $thread = $this->threadFind($id);
        try {
            $this->app->aib()->thread()->manage($thread, $action);
        } catch (\UnexpectedValueException $e) {
            $this->app->abort(500, 'Illegal action');
        }
        $this->threadRedirectTo($thread['id']);
    }

    /**
     * Rename thread
     *
     * @param int $id ID of the thread
     *
     * @return mixed
     */
    public function threadRename($id) {
        $thread = $this->threadFind($id);
        if (isset($_POST['cancel'])) {
            $this->threadRedirectTo($thread['id']);
        }
        if (isset($_POST['submit'])) {
            $theme = isset($_POST['theme']) ? trim($_POST['theme']) : '';
            $error = $this->app->aib()->thread()->checkTheme($theme);
            if ($error === null) {
                $this->app->aib()->thread()->update(['theme' => $theme], 'id', $thread['id']);
                $this->threadRedirectTo($thread['id']);
            } else {
                $error = is_array($error) ? sprintf($this->app->trans($error[0]), $error[1]) : $this->app->trans($error);
            }
        }
        return $this->app->view('panel/imageboard/threadRename', [
            'theme' => $this->app->escape(isset($theme) ? $theme : $thread['theme']),
            'error' => isset($error) ? $error : '',
        ]);
    }

    /**
     * Move thread to the board
     *
     * @param int $id ID of the thread
     *
     * @return mixed
     */
    public function threadMove($id) {
        $thread = $this->threadFind($id);
        if (isset($_POST['cancel'])) {
            $this->threadRedirectTo($thread['id']);
        }
        $boards = array_map(
            function ($board) {
                return $board['name'];
            },
            $this->app->aib()->board()->getList(true)
        );
        $currentBoard = array_search($thread['board'], $boards);
        if ($currentBoard !== false) {
            unset($boards[$currentBoard]);
        }
        if (empty($boards)) {
            $this->threadRedirectTo($thread['id']);
        }
        if (isset($_POST['submit'])) {
            $board = isset($_POST['board']) ? trim($_POST['board']) : '';
            if (in_array($board, $boards)) {
                $this->app->aib()->thread()->update(['board' => $board], 'id', $thread['id']);
                $this->app->aib()->post()->update(['board' => $board], 'thread', $thread['id']);
                $this->app->aib()->image()->move($thread['id'], $board);
                $this->threadRedirectTo($thread['id']);
            } else {
                $error = $this->app->trans('Board does not exists');
            }
        }
        return $this->app->view('panel/imageboard/threadMove', [
            'boards' => $boards,
            'error'  => isset($error) ? $error : '',
        ]);
    }

    /**
     * Find thread
     * If thread is not exists, abort
     *
     * @param int $id ID of the thread
     *
     * @return array|null
     */
    protected function threadFind($id) {
        $thread = $this->app->aib()->thread()->get($id);
        if ($thread === null) {
            $this->app->abort(404, 'Thread does not exists');
        }
        $thread['id'] = (int) $thread['id'];
        return $thread;
    }

    /**
     * Redirect to the thread
     *
     * @param int $id ID of the thread
     *
     * @return void
     */
    protected function threadRedirectTo($id) {
        $this->app->redirect($this->app->url('imageboard.thread.view', ['id' => (int) $id]))->send();
    }

}