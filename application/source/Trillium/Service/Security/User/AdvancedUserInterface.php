<?php

/**
 * Part of the Trillium
 *
 * @author  Kilte Leichnam <nwotnbm@gmail.com>
 * @package Trillium
 */

namespace Trillium\Service\Security\User;

use Symfony\Component\Security\Core\User\UserInterface;

/**
 * AdvancedUserInterface Interface
 *
 * @package Trillium\Service\Security\User
 */
interface AdvancedUserInterface extends UserInterface
{

    /**
     * Create instance
     *
     * @param array $data Data of the user
     *
     * @return self
     */
    public function __construct(array $data);

    /**
     * Returns time of the last user activity
     *
     * @return int
     */
    public function getLastActivity();

    /**
     * Sets time of the last user activity
     *
     * @param $lastActivity
     *
     * @return self
     */
    public function setLastActivity($lastActivity);

    /**
     * Set roles
     *
     * @param array $roles
     *
     * @return self
     */
    public function setRoles(array $roles);

    /**
     * Set password
     *
     * @param string $password
     *
     * @return self
     */
    public function setPassword($password);

    /**
     * Returns data of the user
     * Returns all array, if key is not specified
     * This method must throw \InvalidArgumentException if key is not exists
     *
     * @param string|null $key Key
     *
     * @return mixed
     * @throws \InvalidArgumentException
     */
    public function getData($key = null);

    /**
     * Sets some data to the user
     *
     * @param string $key   Key
     * @param mixed  $value Value
     *
     * @return self
     */
    public function setData($key, $value);

}
