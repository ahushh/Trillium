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
        $errors = [];
        $settings = $request->get('settings', []);
        $settings = array_unique($settings);
        if (empty($settings)) {
            $errors[] = 'Settings can not be empty';
        }
        foreach ($settings as $key => $val) {
            switch ($key) {
                case 'timeshift':
                    if (!is_numeric($val)) {
                        $errors[] = 'Timeshift must be an integer';
                    } else {
                        $val = (int) $val;
                        if ($val < -12 || $val > 12) {
                            $errors[] = sprintf('Timeshift must be from %s to %s', -12, 12);
                        }
                    }
                    break;
                case 'skin':
                    // TODO: Available skins
                    if ($val !== 'default') {
                        $errors[] = sprintf('"%s" skin is not exists', $val);
                    }
                    break;
                default:
                    $errors[] = sprintf('Option "%s" is not supported', $key);
            }
        }

        return !empty($errors) ? ['error' => $errors, '_status' => 400] : ['success' => 'Settings updated'];
    }

}
