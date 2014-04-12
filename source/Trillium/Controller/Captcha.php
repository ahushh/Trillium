<?php

/**
 * Part of the Trillium
 *
 * @author  Kilte Leichnam <nwotnbm@gmail.com>
 * @package Trillium
 */

namespace Trillium\Controller;

use Symfony\Component\HttpFoundation\Response;
use Trillium\Provider\Captcha as Provider;

/**
 * Captcha Class
 *
 * @package Trillium\Controller
 */
class Captcha extends Controller
{

    /**
     * Shows captcha
     *
     * @return Response
     */
    public function show()
    {
        $this->session->set(Provider::SESSION_KEY, $this->captcha->getPhrase());

        return new Response($this->captcha->build()->get(100), 200, ['Content-Type' => 'image/jpeg']);
    }

}
