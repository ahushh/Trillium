<?php

/**
 * Part of the Trillium
 *
 * @author  Kilte Leichnam <nwotnbm@gmail.com>
 * @package Trillium
 */

namespace Trillium\Controller;

/**
 * Home Class
 *
 * @package Trillium\Controller
 */
class Home extends Controller
{

    /**
     * Homepage
     *
     * @return array
     */
    public function index()
    {
        return ['title' => 'Trillium', 'skin' => $this->settings->get('skin')];
    }

}
