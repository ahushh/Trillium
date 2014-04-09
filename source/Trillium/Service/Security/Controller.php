<?php

/**
 * Part of the Trillium
 *
 * @author  Kilte Leichnam <nwotnbm@gmail.com>
 * @package Trillium
 */

namespace Trillium\Service\Security;

use Kilte\AccountManager\Controller\ControllerInterface;
use Kilte\AccountManager\Event\CreateUserBefore;
use Kilte\AccountManager\Event\CreateUserSuccess;
use Kilte\AccountManager\Event\Events;
use Kilte\AccountManager\Event\RemoveUser;
use Kilte\AccountManager\Event\UpdatePassword;
use Kilte\AccountManager\Event\UpdateRoles;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * Controller Class
 *
 * @package Trillium\Service\Security
 */
class Controller implements EventSubscriberInterface
{

    /**
     * @var ControllerInterface A controller instance
     */
    private $controller;

    /**
     * @var EventDispatcherInterface A dispatcher instance
     */
    private $dispatcher;

    /**
     * @var array Security configuration
     */
    private $config;

    /**
     * Constructor
     *
     * @param ControllerInterface      $controller A controller instance
     * @param EventDispatcherInterface $dispatcher A dispatcher instance
     * @param array                    $config     Security configuration
     *
     * @return self
     */
    public function __construct(ControllerInterface $controller, EventDispatcherInterface $dispatcher, array $config)
    {
        $this->controller = $controller;
        $this->dispatcher = $dispatcher;
        $this->config     = $config;
    }

    /**
     * On update user password event
     *
     * @param UpdatePassword $event Event
     *
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     * @throws \InvalidArgumentException
     * @return void
     */
    public function onUpdatePassword(UpdatePassword $event)
    {
        $user = $event->getUser();
        if ($user === null) {
            throw new HttpException(403, 'Unauthorized access');
        }
        if (in_array('ROLE_ROOT', $user->getRoles()) &&
            ($this->controller->getUser()->getUsername() != $user->getUsername())
        ) {
            throw new HttpException(403, 'User is root');
        }
        $request = $event->getRequest();
        if ($request->getMethod() === 'POST') {
            $pwdLen = strlen($request->get('_password_new'));
            if ($pwdLen < 6 || $pwdLen > 40) {
                $event->setErrors(['new' => 'Wrong password len [6-40 expected]']);
            }
        }
    }

    /**
     * Checks request data before create user
     *
     * @param CreateUserBefore $event
     *
     * @throws \InvalidArgumentException
     * @return void
     */
    public function onCreateUserBefore(CreateUserBefore $event)
    {
        $request = $event->getRequest();
        if ($request->getMethod() === 'POST') {
            $errors      = [];
            $username    = $request->get('username', '');
            $password    = $request->get('password', '');
            $roles       = explode(',', $request->get('roles', ''));
            $roles       = array_map('trim', $roles);
            $passLen     = strlen($password);
            $usernameLen = strlen($username);
            if (preg_match('~[^a-z0-9\_\-]~', $username)) {
                $errors['username'] = 'Username must contain the following chars sequences: [a-z][0-9][-_]';
            } elseif ($usernameLen < 2 || $usernameLen > 20) {
                $errors['username'] = sprintf('Wrong username len [%s-%s expected]', 2, 20);
            }
            if ($passLen < 6 || $passLen > 40) {
                $errors['password'] = sprintf('Wrong password len [%s-%s expected]', 6, 40);
            }
            $errors['roles'] = $this->checkRoles($roles);
            if ($errors['roles'] === true) {
                unset($errors['roles']);
            }
            $event->setErrors($errors);
            if (empty($errors)) {
                $this->dispatcher->addListener(
                    Events::CREATE_USER_SUCCESS,
                    function (CreateUserSuccess $event) use ($roles) {
                        $event->getUser()->setRoles($roles);
                    }
                );
            }
        }
    }

    /**
     * On remove user
     *
     * @param RemoveUser $event An event instance
     *
     * @throws HttpException
     * @return void
     */
    public function onRemoveUser(RemoveUser $event)
    {
        $user        = $event->getUser();
        $currentUser = $this->controller->getUser();
        if ($user->getUsername() === $currentUser->getUsername()) {
            throw new HttpException(403, 'Unable to remove yourself');
        }
    }

    /**
     * On update roles
     *
     * @param UpdateRoles $event An event instance
     *
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     * @throws \InvalidArgumentException
     * @return void
     */
    public function onUpdateRoles(UpdateRoles $event)
    {
        $request = $event->getRequest();
        $user    = $event->getUser();
        if (in_array('ROLE_ROOT', $user->getRoles())) {
            throw new HttpException(403, 'User is root');
        }
        if ($request->getMethod() === 'POST') {
            $error = $this->checkRoles($request->get('roles', []));
            if ($error !== true) {
                $event->setErrors(['error' => $error]);
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            Events::UPDATE_PASSWORD    => 'onUpdatePassword',
            Events::CREATE_USER_BEFORE => 'onCreateUserBefore',
            Events::REMOVE_USER        => 'onRemoveUser',
            Events::UPDATE_ROLES       => 'onUpdateRoles',
        ];
    }

    /**
     * Performs check roles
     * Returns true, if roles are valid
     * Otherwise returns error message
     *
     * @param mixed $roles Roles
     *
     * @return boolean|string
     */
    private function checkRoles($roles)
    {
        if (!is_array($roles)) {
            $roles = [$roles];
        }
        if (empty($roles)) {
            $error = 'Roles cannot to be empty';
        } else {
            unset($this->config['roles']['ROLE_ROOT']);
            $notSupported = array_diff($roles, array_keys($this->config['roles']));
            if (!empty($notSupported)) {
                $error = sprintf(
                    'The following roles are not supported: %s',
                    implode(', ', $notSupported)
                );
            }
        }

        return isset($error) ? $error : true;
    }

}
