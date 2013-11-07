<?php

/**
 * Part of the Trillium
 *
 * @package Trillium
 */

namespace Application\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Trillium\Controller\Controller;

/**
 * Trillium Class
 *
 * @package Application\Controller
 */
class Trillium extends Controller {

    /**
     * Mainpage
     *
     * @return string
     */
    public function mainpage() {
        $boards = $this->app['model']('Boards')->getList();
        return $this->app->view('trillium/mainpage', [
            'boards' => $boards,
        ]);
    }

    /**
     * Login action
     *
     * @param Request $request Request
     *
     * @return mixed
     */
    public function login(Request $request) {
        /**
         * @var Session $session
         */
        $session = $this->app['session'];
        $this->app['trillium.pageTitle'] = $this->app->trans('Login');
        return $this->app['view']('trillium/login', [
            'error' => $this->app->trans($this->app['security.last_error']($request)),
            'last_username' => $session->get('_security.last_username'),
        ]);
    }

} 