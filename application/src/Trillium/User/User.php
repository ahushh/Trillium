<?php

/**
 * Part of the Trillium
 *
 * @package Trillium
 */

namespace Trillium\User;

use Symfony\Component\Security\Core\User\AdvancedUserInterface;

/**
 * User Class
 *
 * The user implementation used by the in-memory user provider.
 *
 * @package Trillium\User
 */
class User implements AdvancedUserInterface
{
    /**
     * @var string Username
     */
    private $username;

    /**
     * @var string Password
     */
    private $password;

    /**
     * @var boolean
     */
    private $enabled;

    /**
     * @var boolean
     */
    private $accountNonExpired;

    /**
     * @var boolean
     */
    private $credentialsNonExpired;

    /**
     * @var boolean
     */
    private $accountNonLocked;

    /**
     * @var array Roles
     */
    private $roles;

    /**
     * Create user instance
     *
     * @param string  $username
     * @param string  $password
     * @param array   $roles
     * @param boolean $enabled
     * @param boolean $userNonExpired
     * @param boolean $credentialsNonExpired
     * @param boolean $userNonLocked
     *
     * @return User
     * @throws \InvalidArgumentException
     */
    public function __construct(
        $username,
        $password,
        array $roles = array(),
        $enabled = true,
        $userNonExpired = true,
        $credentialsNonExpired = true,
        $userNonLocked = true
    ) {
        if (empty($username)) {
            throw new \InvalidArgumentException('The username cannot be empty.');
        }

        $this->username = $username;
        $this->password = $password;
        $this->enabled = $enabled;
        $this->accountNonExpired = $userNonExpired;
        $this->credentialsNonExpired = $credentialsNonExpired;
        $this->accountNonLocked = $userNonLocked;
        $this->roles = $roles;
    }

    /**
     * {@inheritdoc}
     */
    public function getRoles()
    {

        return $this->roles;
    }

    /**
     * {@inheritdoc}
     */
    public function getPassword()
    {

        return $this->password;
    }

    /**
     * {@inheritdoc}
     */
    public function getSalt()
    {

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function getUsername()
    {

        return $this->username;
    }

    /**
     * {@inheritdoc}
     */
    public function isAccountNonExpired()
    {

        return $this->accountNonExpired;
    }

    /**
     * {@inheritdoc}
     */
    public function isAccountNonLocked()
    {

        return $this->accountNonLocked;
    }

    /**
     * {@inheritdoc}
     */
    public function isCredentialsNonExpired()
    {

        return $this->credentialsNonExpired;
    }

    /**
     * {@inheritdoc}
     */
    public function isEnabled()
    {

        return $this->enabled;
    }

    /**
     * {@inheritdoc}
     */
    public function eraseCredentials()
    {
    }


    /**
     * Set roles
     *
     * @param array $roles List of the roles
     *
     * @return User
     */
    public function setRoles(array $roles)
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * Set password
     *
     * @param string $password Password
     *
     * @return User
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Set username
     *
     * @param string $username Username
     *
     * @return User
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

}
