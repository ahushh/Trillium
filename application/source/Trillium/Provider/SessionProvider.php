<?php

/**
 * Part of the Trillium
 *
 * @author  Kilte Leichnam <nwotnbm@gmail.com>
 * @package Trillium
 */

namespace Trillium\Provider;

use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\Handler\NativeFileSessionHandler;
use Symfony\Component\HttpFoundation\Session\Storage\NativeSessionStorage;
use Trillium\Service\Session\SessionSubscriber;

/**
 * SessionProvider Class
 *
 * @package Trillium\Provider
 */
class SessionProvider
{

    /**
     * @var Session Session
     */
    private $session;

    /**
     * @var SessionSubscriber SessionSubscriber
     */
    private $subscriber;

    /**
     * Constructor
     *
     * @param array  $options  Storage options
     * @param string $savePath Session save path
     *
     * @return self
     */
    public function __construct(array $options = [], $savePath = null)
    {
        $this->session = new Session(
            new NativeSessionStorage(
                $options,
                new NativeFileSessionHandler($savePath)
            )
        );
        $this->subscriber = new SessionSubscriber($this->session);
    }

    /**
     * Returns the Session instance
     *
     * @return Session
     */
    public function session()
    {
        return $this->session;
    }

    /**
     * Returns the SessionSubscriber instance
     *
     * @return SessionSubscriber
     */
    public function subscriber()
    {
        return $this->subscriber;
    }

}
