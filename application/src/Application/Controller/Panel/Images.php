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
        $image = $this->app->aib()->image()->get($id);
        if ($image === null) {
            $this->app->abort(404, $this->app->trans('Image is not exists'));
        }
        $this->app->aib()->image()->removeFiles([[$image]]);
        $this->app->aib()->image()->remove($id, 'id');
        $this->app->redirect($this->app->url('imageboard.thread.view', ['id' => $image['thread']]))->send();
    }

} 