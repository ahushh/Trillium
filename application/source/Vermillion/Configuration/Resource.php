<?php

/**
 * Part of the Vermillion
 *
 * @author  Kilte Leichnam <nwotnbm@gmail.com>
 * @package Vermillion
 */

namespace Vermillion\Configuration;

/**
 * Resource Class
 *
 * @package Vermillion\Configuration
 */
class Resource
{

    /**
     * @var array Values
     */
    private $values;

    /**
     * Constructor
     *
     * @param array $values Values
     *
     * @return self
     */
    public function __construct(array $values)
    {
        $this->values = $values;
    }

    /**
     * Gets value by key
     *
     * @param string|null $key     Key or null to get all values
     * @param mixed|null  $default Default value, if key is not exists
     *
     * @return mixed|null
     */
    public function get($key = null, $default = null)
    {
        if ($key === null) {
            return $this->values;
        }

        return $this->has($key) ? $this->values[$key] : $default;
    }

    /**
     * Checks, whether value exists
     *
     * @param string $key Key
     *
     * @return boolean
     */
    public function has($key)
    {
        return array_key_exists($key, $this->values);
    }

}
