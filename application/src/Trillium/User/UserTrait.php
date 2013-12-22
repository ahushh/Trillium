<?php

/**
 * Part of the Trillium
 *
 * @package Trillium
 */

namespace Trillium\User;

/**
 * UserTrait Trait
 *
 * @package Trillium\User
 */
trait UserTrait
{
    /**
     * Returns the user manager
     *
     * @return \Trillium\User\UserManager
     */
    public function userManager()
    {
        return $this['user.manager'];
    }

}
