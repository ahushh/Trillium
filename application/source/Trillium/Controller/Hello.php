<?php

/**
 * Part of the Trillium
 *
 * @author Kilte Leichnam <nwotnbm@gmail.com>
 * @package Trillium
 */

namespace Trillium\Controller;

use Symfony\Component\HttpFoundation\Response;
use Trillium\General\Controller;

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
     * @return Response
     */
    public function say($name)
    {
        return new Response('Hello, ' . htmlspecialchars($name, ENT_QUOTES, $this->app->configuration->get('charset')) . '!');
    }

}
