<?php

/**
 * Part of the Trillium
 *
 * @author  Kilte Leichnam <nwotnbm@gmail.com>
 * @package Trillium
 */

namespace Trillium\Service\Settings;

/**
 * Settings Class
 *
 * @package Trillium\Service\Settings
 */
class Settings
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
     * @var array Available skins
     */
    private $availableSkins;

    /**
     * Constructor
     *
     * @param array $availableSkins Available skins
     * @param array $systemSettings System settings
     * @param array $userSettings   User settings
     *
     * @return self
     */
    public function __construct(array $availableSkins, array $systemSettings = [], array $userSettings = [])
    {
        $this->availableSkins = $availableSkins;
        $this->systemSettings = $systemSettings;
        $this->userSettings   = $userSettings;
    }

    /**
     * Sets a value to settings
     * If key is array and, sets all array
     *
     * @param mixed $key   A key
     * @param mixed $value A value
     * @param int   $type  Settings type (Settings::SYSTEM, Settings::USER, Settings::ALL)
     *
     * @throws \InvalidArgumentException
     *
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
     * Creates a settings storage
     *
     * @param int $type A type (Settings::SYSTEM, Settings::USER, Settings::ALL)
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

    /**
     * Returns a value by key
     *
     * If type is Settings::ALL and key is not exists in user settings, tries to get it from system settings.
     * If key could not be found, throws InvalidArgumentException
     *
     * If key is null, returns all settings:
     * Only system for Settings::SYSTEM, only user for Settings::USER and all settings for Settings::ALL.
     *
     * @param mixed $key  A key
     * @param int   $type Settings type (Settings::SYSTEM, Settings::USER, Settings::ALL)
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
     * Performs check
     * Returns array with occurred errors
     *
     * @param array $settings Settings
     *
     * @return array
     */
    public function validate(array $settings)
    {
        $errors   = [];
        $settings = array_unique($settings);
        if (empty($settings)) {
            $errors[] = 'Settings can not be empty';
        }
        foreach ($settings as $key => $val) {
            switch ($key) {
                case 'timeshift':
                    if (!is_numeric($val)) {
                        $errors[] = 'Timeshift must be an integer';
                    } else {
                        $val = (int) $val;
                        if ($val < -12 || $val > 12) {
                            $errors[] = sprintf('Timeshift must be from %s to %s', -12, 12);
                        }
                    }
                    break;
                case 'skin':
                    if (!in_array($val, $this->availableSkins)) {
                        $errors[] = sprintf('"%s" skin is not exists', $val);
                    }
                    break;
                default:
                    $errors[] = sprintf('Option "%s" is not supported', $key);
            }
        }

        return $errors;
    }

}
