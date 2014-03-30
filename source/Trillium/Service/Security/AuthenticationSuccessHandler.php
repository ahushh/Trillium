<?php

/**
 * Part of the Trillium
 *
 * @author  Kilte Leichnam <nwotnbm@gmail.com>
 * @package Trillium
 */

namespace Trillium\Service\Security;

use Kilte\AccountManager\User\AdvancedUserInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authentication\DefaultAuthenticationSuccessHandler;

/**
 * AuthenticationSuccessHandler Class
 *
 * Returns a response with token for a terminal
 *
 * @package Trillium\Service\Security
 */
class AuthenticationSuccessHandler extends DefaultAuthenticationSuccessHandler
{

    /**
     * {@inheritdoc}
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token)
    {
        $user = $token->getUser();
        $lastActivity = '';
        if ($user instanceof AdvancedUserInterface) {
            $lastActivity = "\nLast activity: ";
            if ($user->getLastActivity() > 0) {
                // TODO: time-shift
                $lastActivity .= date('d.m.Y / H:i:s', $user->getLastActivity());
            } else {
                $lastActivity .= 'never';
            }
        }

        return new JsonResponse(['success' => 'You are logged in' . $lastActivity]);
    }

}
