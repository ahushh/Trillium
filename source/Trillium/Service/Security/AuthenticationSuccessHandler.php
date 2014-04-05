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
     * @var callable|null Date formatter
     */
    private $dateFormatter = null;

    /**
     * Sets date formatter
     *
     * @param callback $callable Formatter
     *
     * @return $this
     * @throws \InvalidArgumentException
     */
    public function setDateFormatter($callable)
    {
        if (!is_callable($callable)) {
            throw new \InvalidArgumentException('Formatter is not callable');
        }
        $this->dateFormatter = $callable;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token)
    {
        $user         = $token->getUser();
        $lastActivity = '';
        if ($user instanceof AdvancedUserInterface) {
            $lastActivity = "\nLast activity: ";
            if ($user->getLastActivity() > 0) {
                if ($this->dateFormatter !== null) {
                    $lastActivity .= call_user_func($this->dateFormatter, $user->getLastActivity());
                } else {
                    $lastActivity .= date('d.m.Y / H:i:s', $user->getLastActivity());
                }
            } else {
                $lastActivity .= 'never';
            }
        }

        return new JsonResponse(['success' => 'You are logged in' . $lastActivity]);
    }

}
