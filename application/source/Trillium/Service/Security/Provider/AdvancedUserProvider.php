<?php

/**
 * Part of the Trillium
 *
 * @author  Kilte Leichnam <nwotnbm@gmail.com>
 * @package Trillium
 */

namespace Trillium\Service\Security\Provider;

use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * AdvancedUserProvider Class
 *
 * @package Trillium\Service\Security\Provider
 */
abstract class AdvancedUserProvider implements AdvancedUserProviderInterface
{

    /**
     * Min len of the password
     */
    const PASSWORD_MIN_LEN = 6;

    /**
     * Max len of the password
     */
    const PASSWORD_MAX_LEN = 20;

    /**
     * Min len of the username
     */
    const USERNAME_MIN_LEN = 2;

    /**
     * Max len of the username
     */
    const USERNAME_MAX_LEN = 32;

    /**
     * List of allowed characters in the username
     */
    const USERNAME_ALLOWED_CHARS = '~[^a-z0-9\_\-\@\!]~ius';

    /**
     * @var string Name of the class, which implements AdvancedUserInterface
     */
    protected $supportsClass;

    /**
     * @inheritdoc
     */
    public function refreshUser(UserInterface $user)
    {
        $class = get_class($user);
        if (!$this->supportsClass($class)) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', $class));
        }

        return $this->loadUserByUsername($user->getUsername());
    }

    /**
     * @inheritdoc
     */
    public function supportsClass($class)
    {
        return $class === $this->supportsClass;
    }

    /**
     * @inheritdoc
     */
    public function setSupportsClass($class)
    {
        if (!class_exists($class)) {
            throw new \InvalidArgumentException(sprintf('Class %s does not exists', $class));
        }
        $this->supportsClass = $class;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function checkUsername($username)
    {
        if (mb_strlen($username) < self::USERNAME_MIN_LEN || strlen($username) > self::USERNAME_MAX_LEN) {
            $error = [
                'The length of the value must be in the range of %s to %s characters',
                self::USERNAME_MIN_LEN,
                self::USERNAME_MAX_LEN
            ];
        } elseif (preg_match(self::USERNAME_ALLOWED_CHARS, $username)) {
            $error = ['The value contains disallowed characters. Allowed "%s" characters only', 'a-z 0-9 _ - @ !'];
        } elseif ($this->isUsernameExists($username)) {
            $error = 'Username already exists';
        }

        return isset($error) ? $error : null;
    }

    /**
     * @inheritdoc
     */
    public function checkPassword($password)
    {
        $len = strlen($password);
        if ($len < self::PASSWORD_MIN_LEN || $len > self::PASSWORD_MAX_LEN) {
            return [
                'The length of the value must be in the range of %s to %s characters',
                self::PASSWORD_MIN_LEN,
                self::PASSWORD_MAX_LEN
            ];
        }

        return null;
    }

}
