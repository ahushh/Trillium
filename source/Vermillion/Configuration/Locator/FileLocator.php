<?php

/**
 * Part of the Vermillion
 *
 * @author  Kilte Leichnam <nwotnbm@gmail.com>
 * @package Vermillion
 */

namespace Vermillion\Configuration\Locator;

use Symfony\Component\Config\FileLocatorInterface;

/**
 * FileLocator Class
 *
 * @package Vermillion\Configuration\Locator
 */
class FileLocator implements FileLocatorInterface
{

    /**
     * @var array Paths
     */
    private $paths;

    /**
     * @var array Types
     */
    private $types;

    /**
     * Constructor
     *
     * @param array $paths Paths
     * @param array $types Types
     *
     * @return self
     */
    public function __construct(array $paths, array $types)
    {
        $this->paths = $paths;
        $this->types = $types;
    }

    /**
     * {@inheritdoc}
     */
    public function locate($name, $currentPath = null, $first = true)
    {
        $found = [];
        if ($currentPath !== null) {
            foreach ($this->types as $type) {
                if (is_file($resource = $currentPath . DIRECTORY_SEPARATOR . $name . '.' . $type)) {
                    if ($first) {
                        return $resource;
                    } else {
                        $found[] = $resource;
                    }
                }
            }
        }
        foreach ($this->paths as $path) {
            foreach ($this->types as $type) {
                if (is_file($resource = $path . $name . '.' . $type)) {
                    if ($first === true) {
                        return $resource;
                    }
                    $found[] = $resource;
                }
            }
        }
        if (empty($found)) {
            throw new \InvalidArgumentException(
                sprintf(
                    'The file "%s" does not exist (in: %s%s).',
                    $name,
                    $currentPath !== null ? $currentPath . ', ' : '',
                    implode(', ', $this->paths)
                )
            );
        }

        return $found;
    }

}
