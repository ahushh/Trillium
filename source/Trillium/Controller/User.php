<?php

/**
 * Part of the Trillium
 *
 * @author  Kilte Leichnam <nwotnbm@gmail.com>
 * @package Trillium
 */

namespace Trillium\Controller;

use Symfony\Component\HttpFoundation\Request;

/**
 * User Class
 *
 * @package Trillium\Controller
 */
class User extends Controller
{

    /**
     * Sign In
     *
     * Returns a username and an error of last login
     *
     * @param Request $request A request instance
     *
     * @return array
     */
    public function signIn(Request $request)
    {
        return [
            'username' => $this->session->get('_security.last_username'),
            'error'    => $this->container['security.provider']['last_error']($request)
        ];
    }

}
