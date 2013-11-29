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
        $boards = $this->app->aib()->board()->getList(false);
        $contentPath = RESOURCES_DIR . 'common' . DS . 'mainpage.markdown';
        if (is_file($contentPath)) {
            /** @var $markdown \Knp\Bundle\MarkdownBundle\Parser\MarkdownParser */
            $markdown = $this->app['markdown'];
            $content = $markdown->transformMarkdown(file_get_contents($contentPath));
        }
        return $this->app->view('trillium/mainpage', [
            'boards' => $boards,
            'content' => isset($content) ? $content : '',
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