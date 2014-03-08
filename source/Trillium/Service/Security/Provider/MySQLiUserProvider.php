<?php

/**
 * Part of the Trillium
 *
 * @author  Kilte Leichnam <nwotnbm@gmail.com>
 * @package Trillium
 */

namespace Trillium\Service\Security\Provider;

use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Trillium\Service\Security\User\AdvancedUserInterface;

/**
 * MySQLiUserProvider Class
 *
 * @package Trillium\Service\Security\Provider
 */
class MySQLiUserProvider extends AdvancedUserProvider
{

    /**
     * Name of the table in the database
     */
    const TABLE_NAME = 'users';

    /**
     * @var \mysqli MySQLi instance
     */
    private $db;

    /**
     * Create Model instance
     *
     * @param \mysqli $db MySQLi instance
     *
     * @return self
     */
    public function __construct(\mysqli $db)
    {
        $this->db = $db;
    }

    /**
     * @inheritdoc
     */
    public function loadUserByUsername($username)
    {
        $user = $this->findBy('username', $username);
        if ($user === null) {
            throw new UsernameNotFoundException(sprintf('Username %s does not exists', $username));
        }

        return $user;
    }

    /**
     * @inheritdoc
     */
    public function isUsernameExists($username)
    {
        return $this->countUsers('username', $username, '=') > 0;
    }

    /**
     * @inheritdoc
     *
     * @return self
     */
    public function create(AdvancedUserInterface $user)
    {
        return $this->saveUser($user, true);
    }

    /**
     * @inheritdoc
     *
     * @return self
     */
    public function update(AdvancedUserInterface $user)
    {
        return $this->saveUser($user, false);
    }

    /**
     * @inheritdoc
     */
    public function remove($username)
    {
        $username = $this->db->real_escape_string($username);
        $this->db->query("DELETE FROM `" . self::TABLE_NAME . "` WHERE `username` = '" . $username . "'");

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function count($online = false)
    {
        if ($online === false) {
            return $this->countUsers();
        } else {
            return $this->countUsers('last_activity', time() - self::LAST_ACTIVITY_DELAY, '>');
        }
    }

    /**
     * @inheritdoc
     */
    public function listing($offset = 0, $limit = 0, $online = false)
    {
        return $this->fetchList($offset, $limit, $online ? time() - self::LAST_ACTIVITY_DELAY : null);
    }

    /**
     * Find user by key
     * Returns null, if user is not exists
     *
     * @param string     $key   Key
     * @param int|string $value Value
     *
     * @return AdvancedUserInterface|null
     * @throws \InvalidArgumentException
     */
    protected function findBy($key, $value)
    {
        if (!is_string($key)) {
            throw new \InvalidArgumentException(sprintf('Expects string for argument $key, %s given', gettype($key)));
        }
        if (is_string($value)) {
            $value = $this->db->real_escape_string($value);
        } elseif (!is_int($value)) {
            throw new \InvalidArgumentException(sprintf('Expects string or int for argument $value, %s given', $value));
        }
        $result = $this->db->query("SELECT * FROM `" . self::TABLE_NAME . "` WHERE `" . $key . "` = '" . $value . "'");
        $data = $result->fetch_assoc();
        $result->free();
        if (is_array($data)) {
            $data['roles'] = !empty($data['roles']) ? json_decode($data['roles'], true) : [];
            $className = $this->supportsClass;

            return new $className($data);
        }

        return null;
    }

    /**
     * Returns list of users
     *
     * @param int      $offset       Offset
     * @param int      $limit        Limit
     * @param int|null $lastActivity Filter by last activity (seconds)
     *
     * @throws \InvalidArgumentException
     * @return array
     */
    protected function fetchList($offset = 0, $limit = 0, $lastActivity = null)
    {
        if (!is_int($offset)) {
            throw new \InvalidArgumentException(sprintf(
                'Expects int for argument $offset, %s given',
                gettype($offset)
            ));
        }
        if (!is_int($limit)) {
            throw new \InvalidArgumentException(sprintf('Expects int for argument $offset, %s given', gettype($limit)));
        }
        if (!is_int($lastActivity) && !is_null($lastActivity)) {
            throw new \InvalidArgumentException(sprintf(
                'Expects int or null for argument $lastActivity, %s given',
                gettype($lastActivity)
            ));
        }
        $list = [];
        $result = $this->db->query(
            "SELECT * FROM `" . self::TABLE_NAME . "` "
            . ($lastActivity != null ? " WHERE `last_activity` > '" . $lastActivity . "'" : "")
            . "ORDER BY `username` ASC "
            . ($offset !== 0 || $limit !== 0 ? "LIMIT " . $offset . ", " . $limit : "")
        );
        while (($item = $result->fetch_assoc())) {
            $list[] = $item;
        }
        $result->free();

        return $list;
    }

    /**
     * Returns number of users
     *
     * @param string     $key   Key
     * @param int|string $value Value
     * @param string     $op    Operator (<,>,=,!=)
     *
     * @throws \UnexpectedValueException
     * @throws \InvalidArgumentException
     * @return int
     */
    protected function countUsers($key = '', $value = '', $op = '')
    {
        if (!empty($key) && !empty($value) && !empty($op)) {
            if (!is_string($key)) {
                throw new \InvalidArgumentException(sprintf(
                    'Expects string for argument $key, %s given',
                    gettype($key)
                ));
            }
            if (is_string($value)) {
                $value = $this->db->real_escape_string($value);
            } elseif (!is_int($value)) {
                throw new \InvalidArgumentException(sprintf(
                    'Expects string or int for argument $value, %s given',
                    gettype($value)
                ));
            }
            if (!in_array($op, ['<', '>', '=', '!='])) {
                throw new \UnexpectedValueException('Unexpected value of the argument $op.');
            }
        }
        $result = $this->db->query(
            "SELECT COUNT(*) FROM `" . self::TABLE_NAME . "` "
            . (!empty($key) && !empty($value) && !empty($op) ? "WHERE `" . $key . "` " . $op . " '" . $value . "'" : "")
        );
        $total = (int) $result->fetch_row()[0];
        $result->free();

        return $total;
    }

    /**
     * Save/update user data
     *
     * @param AdvancedUserInterface $user Instance of the user
     * @param boolean               $new  Is new?
     *
     * @return self
     */
    protected function saveUser(AdvancedUserInterface $user, $new)
    {
        $statement = sprintf("%s `" . self::TABLE_NAME . "` SET ", ($new ? "INSERT INTO" : "UPDATE"));
        $data = [];
        foreach ($user->getData() as $key => $value) {
            if (is_string($value)) {
                $value = $this->db->real_escape_string($value);
            } elseif (is_array($value)) {
                $value = $this->db->real_escape_string(json_encode($value));
            }
            $data[$key] = $value;
        }
        $username = '';
        foreach ($data as $key => $value) {
            if ($key == 'username') {
                $username = "`username` = '" . $value . "'";
            } else {
                $statement .= "`" . $key . "` = '" . $value . "',";
            }
        }
        $statement = rtrim($statement, ',');
        $statement .= ($new ? ", " : "WHERE ") . $username;
        $this->db->query($statement);

        return $this;
    }

}
