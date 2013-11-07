<?php

/**
 * Part of the Trillium
 *
 * @package Trillium
 */

namespace Application\Controller\Panel;

use Trillium\Controller\Controller;

/**
 * Users Class
 *
 * Users management
 *
 * @package Application\Controller\Panel
 */
class Users extends Controller {

    /**
     * List of the users
     *
     * @return mixed
     */
    public function usersList() {
        $list = $this->app->userManager()->getList();
        $output = '';
        $viewItem = $this->app->view('panel/users/item')
            ->bind('username', $userName)
            ->bind('role', $userRole);
        foreach ($list as $user) {
            $userName = $this->app->escape($user['username']);
            $user['roles'] = explode(',', $user['roles']);
            $user['roles'] = array_map(array($this->app, 'trans'), $user['roles']);
            $userRole = $this->app->escape(implode(', ', $user['roles']));
            $output .= $viewItem->render();
        }
        $this->app['trillium.pageTitle'] = $this->app->trans('List of the users');
        return $this->app->view('panel/users/list', [
            'list' => $output,
        ]);
    }

    /**
     * Create/Edit user
     *
     * @param string $name ID of the user
     *
     * @return mixed
     */
    public function manage($name) {
        if ($name !== '') {
            $user = $this->app->userManager()->findBy('username', $name);
            if ($user === null) {
                $this->app->abort(404, $this->app->trans('User does not exists'));
            }
            if (in_array('ROLE_ROOT', $user->getRoles())) {
                $this->app->abort(403, $this->app->trans('User is root'));
            }
            $data = ['username' => $user->getUsername(), 'roles' => $user->getRoles(),];
        } else {
            $data = ['username' => '', 'roles' => [],];
        }
        $error = [];
        if (!empty($_POST)) {
            $newData = [
                'roles' => isset($_POST['roles']) && is_array($_POST['roles']) ? $_POST['roles'] : [],
            ];
            if (empty($newData['roles'])) {
                $error['roles'] = $this->app->trans('The value could not be empty');
            } else {
                foreach ($newData['roles'] as $role) {
                    if (!in_array($role, $this->app['user.roles'])) {
                        $error['roles'] = $this->app->trans('The value is incorrect');
                        break;
                    }
                }
            }
            if ($name === '') {
                $newData['username'] = isset($_POST['username']) ? trim($_POST['username']) : '';
                $newData['password'] = isset($_POST['password']) ? trim($_POST['password']) : '';
                if (strlen($newData['password']) < 6) {
                    $error['password'] = sprintf($this->app->trans('The length of the value must be at least %s characters'), 6);
                }
                if (strlen($newData['username']) > 32 || strlen($newData['username']) < 2) {
                    $error['username'] = sprintf($this->app->trans('The length of the value must be in the range of %s to %s characters'), 2, 32);
                } elseif ($this->app->userManager()->isUsernameExists($newData['username'])) {
                    $error['username'] = sprintf($this->app->trans('Username "%s" already exists'), $newData['username']);
                }
            }
            if (empty($error)) {
                if (!isset($user)) {
                    $user = $this->app->userManager()->createUser($newData['username'], $newData['password'], $newData['roles']);
                    $this->app->userManager()->insertUser($user);
                } else {
                    $this->app->userManager()->updateValue($user->getUsername(), 'roles', implode(',', $newData['roles']));
                }
                $this->app->redirect($this->app->url('panel.users'))->send();
            } elseif ($name === '') {
                $data = $newData;
            }
        }
        $this->app['trillium.pageTitle'] = $this->app->trans($name !== '' ? 'Edit user' : 'Create user');
        return $this->app->view('panel/users/manage', [
            'title'    => $this->app['trillium.pageTitle'],
            'data'     => $data,
            'roles'    => $this->app['user.roles'],
            'error'    => $error,
            'create'   => $name === '',
        ]);
    }

    /**
     * Remove user
     *
     * @param string $name Username
     *
     * @return void
     */
    public function remove($name) {
        $user = $this->app->userManager()->findBy('username', $name);
        if ($user === null) {
            $this->app->abort(404, $this->app->trans('User does not exists'));
        }
        if (in_array('ROLE_ROOT', $user->getRoles())) {
            $this->app->abort(403, $this->app->trans('User is root'));
        }
        $this->app->userManager()->deleteUser($name);
        $this->app->redirect($this->app->url('panel.users'))->send();
    }

    /**
     * Change current user's password
     *
     * @return mixed
     */
    public function changePassword() {
        $error = [];
        if (!empty($_POST)) {
            $user = $this->app->user();
            $oldPassword = isset($_POST['old_password']) ? trim($_POST['old_password']) : '';
            $newPassword = isset($_POST['new_password']) ? trim($_POST['new_password']) : '';
            $confirmPassword = isset($_POST['confirm_password']) ? trim($_POST['confirm_password']) : '';
            $oldPassword = $this->app->userManager()->encodePassword($user->getUsername(), $oldPassword);
            if ($oldPassword !== $user->getPassword()) {
                $error['old_password'] = $this->app->trans('Old password is incorrect');
            }
            if (strlen($newPassword) < 6) {
                $error['new_password'] = sprintf($this->app->trans('The length of the value must be at least %s characters'), 6);
            }
            if ($newPassword !== $confirmPassword) {
                $error['confirm_password'] = $this->app->trans('Passwords do not match');
            }
            if (empty($error)) {
                $newPassword = $this->app->userManager()->encodePassword($user->getUsername(), $newPassword);
                $this->app->userManager()->updateValue($user->getUsername(), 'password', $newPassword);
                $this->app->redirect($this->app->url('login'))->send();
            }
        }
        $this->app['trillium.pageTitle'] = $this->app->trans('Change password');
        return $this->app->view('panel/users/changePassword', ['error' => $error]);
    }

}