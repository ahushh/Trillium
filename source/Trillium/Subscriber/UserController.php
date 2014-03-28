<?php

/**
 * Part of the Trillium
 *
 * @author  Kilte Leichnam <nwotnbm@gmail.com>
 * @package Trillium
 */

namespace Trillium\Subscriber;

use Kilte\AccountManager\Controller\ControllerInterface;
use Kilte\AccountManager\Event\Events;
use Kilte\AccountManager\Event\UpdatePassword;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * UserController Class
 *
 * @package Trillium\Subscriber
 */
class UserController implements EventSubscriberInterface
{

    /**
     * @var ControllerInterface A controller instance
     */
    private $controller;

    /**
     * Constructor
     *
     * @param ControllerInterface $controller A controller instance
     *
     * @return self
     */
    public function __construct(ControllerInterface $controller)
    {
        $this->controller = $controller;
    }

    /**
     * On update user password event
     *
     * @param UpdatePassword $event Event
     *
     * @throws HttpException
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
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            Events::UPDATE_PASSWORD => 'onUpdatePassword',
        ];
    }

}
