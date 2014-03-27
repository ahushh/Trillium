<?php

/**
 * Part of the Trillium
 *
 * @author  Kilte Leichnam <nwotnbm@gmail.com>
 * @package Trillium
 */

namespace Trillium\Subscriber;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authentication\DefaultAuthenticationSuccessHandler;

/**
 * AuthenticationSuccessHandler Class
 *
 * Returns a response with token for a terminal
 *
 * @package Trillium\Subscriber
 */
class AuthenticationSuccessHandler extends DefaultAuthenticationSuccessHandler
{

    /**
     * {@inheritdoc}
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token)
    {
        return new JsonResponse(['token' => $request->cookies->get('keep_auth')]);
    }

}
