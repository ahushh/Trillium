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
        $list = $this->app->aib()->board()->getList();
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
                $this->app->redirect($this->app->url('panel.boards'))->send();
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
        return $this->app->view('panel/boards/manage', [
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
    public function remove($name) {
        $this->app->aib()->removeBoard($name);
        $this->app->redirect($this->app->url('panel.boards'))->send();
    }

}