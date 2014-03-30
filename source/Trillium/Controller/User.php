<?php

/**
 * Part of the Trillium
 *
 * @author  Kilte Leichnam <nwotnbm@gmail.com>
 * @package Trillium
 */

namespace Trillium\Controller;

use Kilte\AccountManager\Exception\AccessDeniedException;
use Kilte\AccountManager\Exception\UserNotFoundException;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Security\Http\RememberMe\RememberMeServicesInterface;

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

    /**
     * Updates password for an user
     *
     * @param Request $request  A request instance
     * @param string  $username Username
     *
     * @throws HttpException
     * @return array
     */
    public function editPassword(Request $request, $username)
    {
        try {
            $result = $this->userController->updatePassword($request, $username);

            return !is_array($result) ? [] : $result;
        } catch (UserNotFoundException $e) {
            throw new HttpException(404, $e->getMessage());
        }
    }

    /**
     * Checks whether user is logged in
     * Returns isAuthorized flag and an username
     *
     * @return array
     */
    public function isAuthorized()
    {
        try {
            $user = $this->userController->getUser();
            $return = ['isAuthorized' => true, 'username' => $user->getUsername()];
        } catch (AccessDeniedException $e) {
            $return = ['isAuthorized' => false, 'username' => null];
        }

        return $return;
    }

}
