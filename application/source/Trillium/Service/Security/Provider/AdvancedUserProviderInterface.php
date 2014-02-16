<?php

/**
 * Part of the Trillium
 *
 * @author  Kilte Leichnam <nwotnbm@gmail.com>
 * @package Trillium
 */

namespace Trillium\Service\Security\Provider;

use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Trillium\Service\Security\User\AdvancedUserInterface;

/**
 * AdvancedUserProviderInterface Interface
 *
 * @package Trillium\Service\Security\Provider
 */
interface AdvancedUserProviderInterface extends UserProviderInterface
{

    /**
     * Delay of the last activity in seconds
     */
    const LAST_ACTIVITY_DELAY = 300;

    /**
     * Sets name of the class, which implements AdvancedUserInterface
     *
     * @param string $className
     *
     * @throws \InvalidArgumentException Class does not exists
     * @return $this
     */
    public function setSupportsClass($className);

    /**
     * Loads the user for the given username.
     *
     * This method must throw UsernameNotFoundException if the user is not
     * found.
     *
     * @param string $username The username
     *
     * @return AdvancedUserInterface
     *
     * @see UsernameNotFoundException
     *
     * @throws UsernameNotFoundException if the user is not found
     */
    public function loadUserByUsername($username);

    /**
     * Checks username for exists
     *
     * Returns true, if exists. Otherwise returns false.
     *
     * @param string $username
     *
     * @return boolean
     */
    public function isUsernameExists($username);

    /**
     * Update user data
     *
     * @param AdvancedUserInterface $user
     */
    public function update(AdvancedUserInterface $user);

    /**
     * Create new user
     *
     * @param AdvancedUserInterface $user
     */
    public function create(AdvancedUserInterface $user);

    /**
     * Remove user by username
     *
     * @param string $username
     */
    public function remove($username);

    /**
     * Returns number of users
     *
     * @param boolean $online Online only?
     *
     * @return int
     */
    public function count($online = false);

    /**
     * Returns list of users
     *
     * @param int     $offset Offset
     * @param int     $limit  Limit
     * @param boolean $online Online only?
     *
     * @return array
     */
    public function listing($offset = 0, $limit = 0, $online = false);

    /**
     * Perform check username
     * Returns null, if username is valid, otherwise username is invalid
     *
     * @param string $username Username
     *
     * @return mixed|null
     */
    public function checkUsername($username);

    /**
     * Check password length
     * Returns null, if password is valid, otherwise password is invalid
     *
     * @param string $password Password
     *
     * @return mixed|null
     */
    public function checkPassword($password);

}