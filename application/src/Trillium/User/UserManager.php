<?php

/**
 * Part of the Trillium
 *
 * @package Trillium
 */

namespace Trillium\User;

use Symfony\Component\Security\Core\Encoder\EncoderFactory;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Trillium\Exception\InvalidArgumentException;
use Trillium\MySQLi\MySQLi;

/**
 * UserManager Class
 *
 * Managing users
 *
 * @package Trillium\User
 */
class UserManager implements UserProviderInterface {

    /**
     * @var MySQLi MySQLi object
     */
    private $db;

    /**
     * @var EncoderFactory Encoder factory
     */
    private $encoderFactory;

    /**
     * Create UserManager instance
     *
     * @param MySQLi         $db             MySQLi object
     * @param EncoderFactory $encoderFactory Encoder factory
     *
     * @return UserManager
     */
    public function __construct(MySQLi $db, EncoderFactory $encoderFactory) {
        $this->db = $db;
        $this->encoderFactory = $encoderFactory;
    }

    /**
     * Loads the user for the given username.
     *
     * This method must throw UsernameNotFoundException if the user is not
     * found.
     *
     * @param string $username The username
     *
     * @return User
     *
     * @see UsernameNotFoundException
     *
     * @throws UsernameNotFoundException if the user is not found
     *
     */
    public function loadUserByUsername($username) {
        $user = $this->findBy('username', $username);
        if ($user === null) {
            throw new UsernameNotFoundException(sprintf('Username %s does not exists', $username));
        }
        return $user;
    }

    /**
     * Refreshes the user for the account interface.
     *
     * It is up to the implementation to decide if the user data should be
     * totally reloaded (e.g. from the database), or if the UserInterface
     * object can just be merged into some internal array of users / identity
     * map.
     *
     * @param UserInterface $user
     *
     * @return User
     *
     * @throws UnsupportedUserException if the account is not supported
     */
    public function refreshUser(UserInterface $user) {
        if (!$this->supportsClass(get_class($user))) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', get_class($user)));
        }
        return $this->loadUserByUsername($user->getUsername());
    }

    /**
     * Whether this provider supports the given user class
     *
     * @param string $class
     *
     * @return Boolean
     */
    public function supportsClass($class) {
        return $class === 'Trillium\User\User';
    }

    /**
     * Find one user
     *
     * @param string     $key   Find by
     * @param string|int $value The value
     *
     * @throws InvalidArgumentException
     * @return User|null
     */
    public function findBy($key, $value) {
        $value = is_numeric($value) ? (int) $value : (is_string($value) ? $this->db->real_escape_string($value) : null);
        if ($value === null) {
            throw new InvalidArgumentException('value', 'string, integer', gettype($value));
        }
        $where = "WHERE `$key` = '$value'";
        $result = $this->db->query("SELECT * FROM `users` $where LIMIT 0, 1");
        $data = $result->fetch_assoc();
        $result->free();
        if (is_array($data)) {
            $data['roles'] = explode(',', $data['roles']);
        }
        return is_array($data) ? $this->userObject($data) : null;
    }

    /**
     * Create the new user
     *
     * @param string  $username       Username
     * @param string  $password       Plain password
     * @param array   $roles          Roles
     * @param boolean $encodePassword Encode password or not
     *
     * @return User
     */
    public function createUser($username, $password, array $roles, $encodePassword = true) {
        if ($encodePassword) {
            $password = $this->encodePassword($username, $password);
        }
        $user = $this->userObject(['username' => $username, 'password' => $password, 'roles' => $roles]);
        return $user;
    }

    /**
     * Save user data
     *
     * @param User $user Data of the user
     *
     * @return void
     */
    public function insertUser(User $user) {
        $username = $this->db->real_escape_string($user->getUsername());
        $password = $this->db->real_escape_string($user->getPassword());
        $roles = $this->db->real_escape_string(implode(',', $user->getRoles()));
        $this->db->query("INSERT INTO `users` SET `username` = '$username', `password` = '$password', `roles` = '$roles'");
    }

    /**
     * Update data item of the user
     *
     * @param string     $username Username
     * @param string     $key      Key
     * @param string|int $value    Value
     *
     * @throws InvalidArgumentException
     * @return void
     */
    public function updateValue($username, $key, $value) {
        if (!is_string($value) && !is_int($value)) {
            throw new InvalidArgumentException('value', 'string, integer', gettype($value));
        }
        $username = $this->db->real_escape_string($username);
        $value = is_numeric($value) ? (int) $value : (is_string($value) ? $this->db->real_escape_string($value) : $value);
        $this->db->query("UPDATE `users` SET `" . $key . "` = '" .$value . "' WHERE `username` = '" . $username . "'");
    }

    /**
     * Remove user data
     *
     * @param string $username Username
     *
     * @return void
     */
    public function deleteUser($username) {
        $username = $this->db->real_escape_string($username);
        $this->db->query("DELETE FROM `users` WHERE `username` = '$username'");
    }

    /**
     * Check username for exists
     *
     * @param string $username Username
     *
     * @return boolean
     */
    public function isUsernameExists($username) {
        $username = $this->db->real_escape_string($username);
        $result = $this->db->query("SELECT COUNT(*) FROM `users` WHERE `username` = '$username'");
        $isExists = (bool) $result->fetch_row()[0];
        $result->free();
        return $isExists;
    }

    /**
     * Get list of the users
     *
     * @param array $limit Limit [offset => int, limit => int]
     * @param array $order Order [by => string, direction => string]
     *
     * @return array
     */
    public function getList(array $limit = [], array $order = []) {
        $limit = isset($limit['offset'], $limit['limit']) ? "LIMIT " . (int) $limit['offset'] . ", " . (int) $limit['limit'] : "";
        $order = isset($order['by'], $order['direction']) ? "ORDER BY `" . $order['by'] . "` " . $order['direction'] : "";
        $result = $this->db->query("SELECT * FROM `users` $limit $order");
        $list = [];
        while (($user = $result->fetch_assoc())) {
            $list[] = $user;
        }
        $result->free();
        return $list;
    }

    /**
     * Encode password
     *
     * @param string $username      Username
     * @param string $plainPassword Plain password
     *
     * @return string
     */
    public function encodePassword($username, $plainPassword) {
        $user = new User($username, $plainPassword);
        return $this->encoderFactory->getEncoder($user)->encodePassword($plainPassword, $user->getSalt());
    }

    /**
     * Create user object from stored data
     *
     * @param array $data Data of the user
     *
     * @return User
     */
    protected function userObject(array $data) {
        return new User($data['username'], $data['password'], $data['roles'], true, true, true, true);
    }

}