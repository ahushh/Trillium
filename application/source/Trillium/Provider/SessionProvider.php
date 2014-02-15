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
     * @var array Storage options
     */
    private $options;

    /**
     * @var string|null Session save path
     */
    private $savePath;

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
        $this->options  = $options;
        $this->savePath = $savePath;
    }

    /**
     * Returns the Session instance
     *
     * @return Session
     */
    public function session()
    {
        if ($this->session === null) {
            $this->session = new Session(
                new NativeSessionStorage(
                    $this->options,
                    new NativeFileSessionHandler($this->savePath)
                )
            );
        }

        return $this->session;
    }

    /**
     * Returns the SessionSubscriber instance
     *
     * @return SessionSubscriber
     */
    public function subscriber()
    {
        if ($this->subscriber === null) {
            $this->subscriber = new SessionSubscriber($this->session());
        }

        return $this->subscriber;
    }

}
