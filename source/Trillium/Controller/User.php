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
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;

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
            $user   = $this->userController->getUser();
            $return = ['isAuthorized' => true, 'username' => $user->getUsername()];
        } catch (AccessDeniedException $e) {
            $return = ['isAuthorized' => false, 'username' => null];
        }

        return $return;
    }

    /**
     * Returns list of users
     *
     * @return array
     */
    public function listing()
    {
        $list = [];
        $availableRoles = $this->configuration->load('security')->get('roles');
        foreach ($this->userController->listing() as $user) {
            $roles = array_map(
                function ($role) use ($availableRoles) {
                    return isset($availableRoles[$role]) ? $availableRoles[$role] : $role;
                },
                $user->getRoles()
            );
            $list[] = [
                'username'      => $user->getUsername(),
                'roles'         => implode(', ', $roles),
                'last_activity' => date('d.m.y / H:i:s', $user->getLastActivity()), // TODO: time-shift
            ];
        }

        return $list;
    }

    /**
     * Creates a user
     *
     * @param Request $request
     *
     * @return array
     */
    public function create(Request $request)
    {
        $result = $this->userController->create($request);

        return $result === true ? ['success' => 'User created'] : $result;
    }

}
