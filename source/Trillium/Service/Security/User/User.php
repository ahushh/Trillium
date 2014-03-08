<?php

/**
 * Part of the Trillium
 *
 * @author  Kilte Leichnam <nwotnbm@gmail.com>
 * @package Trillium
 */

namespace Trillium\Service\Security\User;

/**
 * User Class
 *
 * @package Trillium\Service\Security\User
 */
class User implements AdvancedUserInterface
{

    /**
     * @var array Contains all data of the user
     */
    private $data;

    /**
     * @inheritdoc
     */
    public function __construct(array $data)
    {
        if (!isset($data['last_activity'])) {
            $data['last_activity'] = time();
        }
        $this->data = $data;
    }

    /**
     * @inheritdoc
     */
    public function getRoles()
    {
        return $this->data['roles'];
    }

    /**
     * @inheritdoc
     */
    public function getPassword()
    {
        return $this->data['password'];
    }

    /**
     * @inheritdoc
     */
    public function getSalt()
    {
        return null;
    }

    /**
     * @inheritdoc
     */
    public function getUsername()
    {
        return $this->data['username'];
    }

    /**
     * @inheritdoc
     */
    public function eraseCredentials()
    {
    }

    /**
     * @inheritdoc
     */
    public function getLastActivity()
    {
        return $this->data['last_activity'];
    }

    /**
     * @inheritdoc
     */
    public function setLastActivity($lastActivity)
    {
        if (!is_int($lastActivity)) {
            throw new \InvalidArgumentException('Expects int, ' . gettype($lastActivity) . ' given.');
        }
        $this->data['last_activity'] = $lastActivity;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function setRoles(array $roles)
    {
        $this->data['roles'] = $roles;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function setPassword($password)
    {
        $this->data['password'] = $password;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getData($key = null)
    {
        if ($key !== null) {
            if (!array_key_exists($key, $this->data)) {
                throw new \InvalidArgumentException(sprintf('Invalid key %s given', $key));
            }

            return $this->data[$key];
        }

        return $this->data;
    }

    /**
     * @inheritdoc
     */
    public function setData($key, $value)
    {
        $this->data[$key] = $value;

        return $this;
    }

}
