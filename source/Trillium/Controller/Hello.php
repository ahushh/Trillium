<?php

/**
 * Part of the Trillium
 *
 * @author  Kilte Leichnam <nwotnbm@gmail.com>
 * @package Trillium
 */

namespace Trillium\Controller;

/**
 * Hello Class
 *
 * Temporary controller
 *
 * @package Trillium\Controller
 */
class Hello extends Controller
{

    /**
     * Says hello with a given name
     *
     * @param string $name Name
     *
     * @return array
     */
    public function say($name)
    {
        $message = 'hello.%name%';
        $message = $this->container['translator']->trans($message, ['%name%' => $name]);

        return ['message' => $message];
    }

}
