<?php

/**
 * Part of the Trillium
 *
 * @package Trillium
 */

namespace Application\Controller\Panel;


use Trillium\Controller\Controller;

/**
 * Images Class
 *
 * Images management
 *
 * @package Application\Controller\Panel
 */
class Images extends Controller {

    /**
     * Remove image
     *
     * @param int $id ID of the image
     *
     * @return void
     */
    public function remove($id) {
        $id = (int) $id;
        $image = $this->app->ibImage()->get($id);
        if ($image === null) {
            $this->app->abort(404, $this->app->trans('Image is not exists'));
        }
        $path = $this->app['imageboard.resources_path'] . $image['board'] . DS . $image['name'] . '%s.' . $image['ext'];
        $full = sprintf($path, '');
        $small = sprintf($path, '_small');
        if (is_file($full)) {
            unlink($full);
        }
        if (is_file($small)) {
            unlink($small);
        }
        $this->app->ibImage()->remove($id, 'id');
        $this->app->redirect($this->app->url('imageboard.thread.view', ['id' => $image['thread']]))->send();
    }

} 