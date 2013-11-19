<?php

/**
 * Part of the Trillium
 *
 * @package Trillium
 */

namespace Application\Controller\Panel;


use Trillium\Controller\Controller;

/**
 * Boards Class
 *
 * Managing boards
 *
 * @package Application\Controller\Panel
 */
class Boards extends Controller {

    /**
     * List of the boards
     *
     * @return mixed
     */
    public function boardsList() {
        $output = '';
        $list = $this->app->ibBoard()->getList();
        if (!empty($list)) {
            $itemWrapper = $this->app->view('panel/boards/item')->bind('name', $boardName)->bind('summary', $boardSummary);
            foreach ($list as $board) {
                $boardName = $this->app->escape($board['name']);
                $boardSummary = $this->app->escape($board['summary']);
                $output.= $itemWrapper->render();
            }
        }
        return $this->app->view('panel/boards/list', ['list' => $output]);
    }

    /**
     * Create or update board
     *
     * @param string $name Name of the board
     *
     * @return mixed
     */
    public function manage($name = '') {
        $error = [];
        if ($name !== '') {
            $data = $this->app->ibBoard()->get($name);
            if ($data === null) {
                $this->app->abort(404, 'Board does not exists');
            }
        } else {
            $data = [
                'name'             => '',
                'summary'          => '',
                'max_file_size'    => 0,
                'images_per_post'  => 0,
                'thumb_width'      => 0,
                'pages'            => 0,
                'threads_per_page' => 0,
            ];
        }
        if (!empty($_POST)) {
            $newData = [];
            if ($name === '') {
                $newData['name'] = isset($_POST['name']) ? trim($_POST['name']) : '';
                if (preg_match('~[^a-z\d]~i', $newData['name'])) {
                    $error['name'] = $this->app->trans('Value must contain only latin characters and numbers');
                } elseif (strlen($newData['name']) < 1 || strlen($newData['name']) > 10) {
                    $error['name'] = sprintf($this->app->trans('The length of the value must be in the range of %s to %s characters'), 1, 10);
                } elseif ($this->app->ibBoard()->isExists($newData['name'])) {
                    $error['name'] = $this->app->trans('Board already exists');
                }
            } else {
                $newData['name'] = $name;
            }
            $newData['summary'] = isset($_POST['summary']) ? trim($_POST['summary']) : '';
            if (strlen($newData['summary']) > 200) {
                $error['summary'] = sprintf($this->app->trans('The length of the value must not exceed %s characters'), 200);
            }
            $newData['max_file_size'] = isset($_POST['max_file_size']) ? (int) $_POST['max_file_size'] : 0;
            if ($newData['max_file_size'] > 10240 || $newData['max_file_size'] < 1024) {
                $error['max_file_size'] = sprintf($this->app->trans('The value must be between %s and %s'), 1024, 10240);
            }
            $newData['images_per_post'] = isset($_POST['images_per_post']) ? (int) $_POST['images_per_post'] : 0;
            if ($newData['images_per_post'] > 10 || $newData['images_per_post'] < 1) {
                $error['images_per_post'] = sprintf($this->app->trans('The value must be between %s and %s'), 1, 10);
            }
            $newData['thumb_width'] = isset($_POST['thumb_width']) ? (int) $_POST['thumb_width'] : 0;
            if ($newData['thumb_width'] < 64 || $newData['thumb_width'] > 999) {
                $error['thumb_width'] = sprintf($this->app->trans('The value must be between %s and %s'), 64, 999);
            }
            $newData['pages'] = isset($_POST['pages']) ? (int) $_POST['pages'] : 0;
            if ($newData['pages'] < 1 || $newData['pages'] > 99) {
                $error['pages'] = sprintf($this->app->trans('The value must be between %s and %s'), 1, 99);
            }
            $newData['threads_per_page'] = isset($_POST['threads_per_page']) ? (int) $_POST['threads_per_page'] : 0;
            if ($newData['threads_per_page'] < 1 || $newData['threads_per_page'] > 99) {
                $error['threads_per_page'] = sprintf($this->app->trans('The value must be between %s and %s'), 1, 99);
            }
            if (empty($error)) {
                $newData['max_file_size'] = $newData['max_file_size'] * 1024;
                $this->app->ibBoard()->save($newData);
                if ($name === '') {
                    mkdir($this->app['imageboard.resources_path'] . $newData['name']);
                }
                $this->app->redirect($this->app->url('panel.boards'))->send();
            }
        }
        $data['max_file_size'] = $data['max_file_size'] / 1024;
        return $this->app->view('panel/boards/manage', [
            'error' => $error,
            'data' => $data,
            'edit' => $name !== '',
        ]);
    }

    /**
     * Remove board
     *
     * @param string $name Name of the board
     *
     * @return void
     */
    public function remove($name) {
        $this->app->ibBoard()->remove($name);
        rmdir($this->app['imageboard.resources_path'] . $name);
        $this->app->redirect($this->app->url('panel.boards'))->send();
    }

}