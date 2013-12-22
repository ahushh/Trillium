<?php

/**
 * Part of the Trillium
 *
 * @package Trillium
 */

namespace Application\Controller;

use Trillium\Controller\Controller;

/**
 * Panel Class
 *
 * @package Application\Controller
 */
class Panel extends Controller
{
    /**
     * Index page of the control panel
     *
     * @return mixed
     */
    public function menu()
    {
        $this->app['trillium.pageTitle'] = $this->app->trans('Control panel');

        return $this->app->view('panel/menu');
    }

    /**
     * Edit mainpage content
     *
     * @return mixed
     */
    public function mainpage()
    {
        $error = '';
        $filePath = RESOURCES_DIR . 'common' . DS . 'mainpage.markdown';
        $text = is_file($filePath) ? file_get_contents($filePath) : '';
        if (!empty($_POST)) {
            $text = isset($_POST['text']) ? trim($_POST['text']) : '';
            if (empty($text)) {
                $error = $this->app->trans('The value could not be empty');
            } elseif (strlen($text) > 5000) {
                $error = sprintf($this->app->trans('The length of the value must not exceed %s characters'), 5000);
            }
            if (empty($error)) {
                file_put_contents($filePath, $text);
                $this->app->redirect($this->app->url('panel'))->send();
            }
        }

        return $this->app->view('panel/mainpage', [
            'error' => $error,
            'text'  => $text,
        ]);
    }

}
