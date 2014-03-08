<?php

/**
 * Part of the Trillium
 *
 * @author  Kilte Leichnam <nwotnbm@gmail.com>
 * @package Trillium
 */

namespace Trillium\Controller;

use Symfony\Component\HttpFoundation\Response;
use Vermillion\Controller\Controller;

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
        $message = 'hello.%name%';
        /*$name = htmlspecialchars($name, ENT_QUOTES, $this->app->configuration->get('charset'));
        $message = $this->app->translator->trans($message, ['%name%' => $name]);
        $message = $this->app->view->render('sayHello.twig', ['message' => $message]);*/

        return new Response($message);
    }

}
