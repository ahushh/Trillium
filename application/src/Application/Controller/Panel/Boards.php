<?php

/**
 * Part of the Trillium
 *
 * @package Trillium
 */

namespace Application\Controller\Panel;


use Trillium\Controller\Controller;
use Trillium\Silex\Application;

/**
 * Boards Class
 *
 * Managing boards
 *
 * @package Application\Controller\Panel
 */
class Boards extends Controller {

    /**
     * @var \Application\Model\Boards Model
     */
    private $model;

    /**
     * Create the Boards instance
     * @param Application $app Instance of the Application
     * @return Boards
     */
    public function __construct(Application $app) {
        parent::__construct($app);
        $this->model = $this->app['model']('Boards');
    }

    /**
     * List of the boards
     *
     * @return mixed
     */
    public function boardsList() {
        $output = '';
        $list = $this->model->getList();
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
            $data = $this->model->get($name);
            if ($data === null) {
                $this->app->abort(404, 'Board does not exists');
            }
        } else {
            $data = [
                'name' => '',
                'summary' => '',
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
                } elseif ($this->model->isExists($newData['name'])) {
                    $error['name'] = $this->app->trans('Board already exists');
                }
            } else {
                $newData['name'] = $name;
            }
            $newData['summary'] = isset($_POST['summary']) ? trim($_POST['summary']) : '';
            if (strlen($newData['summary']) > 200) {
                $error['summary'] = sprintf($this->app->trans('The length of the value must not exceed %s characters'), 200);
            }
            if (empty($error)) {
                $this->model->save($newData);
                $this->app->redirect($this->app->url('panel.boards'))->send();
            }
        }
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
        $this->model->remove($name);
        $this->app->redirect($this->app->url('panel.boards'))->send();
    }

}