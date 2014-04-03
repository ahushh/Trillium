<?php

/**
 * Part of the Trillium
 *
 * @author  Kilte Leichnam <nwotnbm@gmail.com>
 * @package Trillium
 */

namespace Trillium\Service\Settings;

/**
 * Storage Class
 *
 * @package Trillium\Service\Settings
 */
class Storage
{

    /**
     * Associated with all settings
     */
    const ALL = -1;

    /**
     * Associated with user settings
     */
    const USER = 0;

    /**
     * Associated with system settings
     */
    const SYSTEM = 1;

    /**
     * @var array System settings
     */
    private $systemSettings;

    /**
     * @var array User settings
     */
    private $userSettings;

    /**
     * Constructor
     *
     * @param array $systemSettings System settings
     * @param array $userSettings   User settings
     *
     * @return self
     */
    public function __construct(array $systemSettings = [], array $userSettings = [])
    {
        $this->systemSettings = $systemSettings;
        $this->userSettings   = $userSettings;
    }

    /**
     * Sets a value to settings
     * If key is array and, sets all array
     *
     * @param mixed $key   A key
     * @param mixed $value A value
     * @param int   $type  Settings type (Storage::SYSTEM, Storage::USER, Storage::ALL)
     *
     * @throws \InvalidArgumentException
     * @return $this
     */
    public function set($key, $value = null, $type = self::ALL)
    {
        $storage = $this->createStorage($type);
        foreach ($storage as &$settings) {
            if (is_array($key)) {
                $settings = $key;
            } else {
                $settings[$key] = $value;
            }
        }

        return $this;
    }

    /**
     * Returns a value by key
     *
     * If type is Storage::ALL and key is not exists in user settings, tries to get it from system settings.
     * If key could not be found, throws InvalidArgumentException
     *
     * If key is null, returns all settings:
     * Only system for Storage::SYSTEM, only user for Storage::USER and all settings for Storage::ALL.
     *
     * @param mixed $key  A key
     * @param int   $type Settings type (Storage::SYSTEM, Storage::USER, Storage::ALL)
     *
     * @throws \InvalidArgumentException
     * @return mixed
     */
    public function get($key = null, $type = self::USER)
    {
        $storage = $this->createStorage($type);
        if ($key === null) {
            return $type !== self::ALL ? $storage[$type] : $storage;
        } else {
            foreach ($storage as $settings) {
                if (array_key_exists($key, $settings)) {
                    return $settings[$key];
                }
            }
        }
        throw new \InvalidArgumentException(sprintf('Key "%s" is not exists', $key));
    }

    /**
     * Creates a settings storage
     *
     * @param int $type A type (Storage::SYSTEM, Storage::USER, Storage::ALL)
     *
     * @return array
     * @throws \InvalidArgumentException
     */
    private function createStorage($type)
    {
        $storage = [];
        if ($type === self::USER || $type === self::ALL) {
            $storage[self::USER] =& $this->userSettings;
        }
        if ($type === self::SYSTEM || $type === self::ALL) {
            $storage[self::SYSTEM] =& $this->systemSettings;
        }
        if (empty($storage)) {
            throw new \InvalidArgumentException('Unknown settings type');
        }

        return $storage;
    }

}
