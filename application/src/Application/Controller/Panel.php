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
class Panel extends Controller {

    /**
     * Index page of the control panel
     *
     * @return mixed
     */
    public function menu() {
        $this->app['trillium.pageTitle'] = $this->app->trans('Control panel');
        return $this->app['view']('panel/menu');
    }



}