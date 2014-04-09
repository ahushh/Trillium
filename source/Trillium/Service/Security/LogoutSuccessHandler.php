<?php

/**
 * Part of the Trillium
 *
 * @author  Kilte Leichnam <nwotnbm@gmail.com>
 * @package Trillium
 */

namespace Trillium\Service\Security;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Logout\LogoutSuccessHandlerInterface;

/**
 * LogoutSuccessHandler Class
 *
 * @package Trillium\Service\Security
 */
class LogoutSuccessHandler implements LogoutSuccessHandlerInterface
{

    /**
     * {@inheritdoc}
     */
    public function onLogoutSuccess(Request $request){
        return new JsonResponse(['success' => 'Goodbye!']);
    }

}
