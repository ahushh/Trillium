<?php

/**
 * Part of the Trillium
 *
 * @author  Kilte Leichnam <nwotnbm@gmail.com>
 * @package Trillium
 */

namespace Trillium\Controller;

use Symfony\Component\HttpFoundation\Request;

/**
 * Settings Class
 *
 * @package Trillium\Controller
 */
class Settings extends Controller
{

    /**
     * Validate settings
     *
     * @param Request $request A request instance
     *
     * @return array
     */
    public function validate(Request $request)
    {
        $errors = $this->settings->validate($request->get('settings', []));

        return !empty($errors) ? ['error' => $errors, '_status' => 400] : [];
    }

    /**
     * Returns list of available skins
     *
     * @return array
     */
    public function skins()
    {
        return $this->configuration->get('available_skins');
    }

}
