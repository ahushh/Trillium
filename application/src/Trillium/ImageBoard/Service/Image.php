<?php

/**
 * Part of the Trillium
 *
 * @package Trillium
 */

namespace Trillium\ImageBoard\Service;

/**
 * Image Class
 *
 * @package Trillium\ImageBoard\Service
 */
class Image {

    /**
     * @var \Trillium\ImageBoard\Model\Image Model
     */
    private $model;

    /**
     * Create instance
     *
     * @param \Trillium\ImageBoard\Model\Image $model Model
     *
     * @return Image
     */
    public function __construct(\Trillium\ImageBoard\Model\Image $model) {
        $this->model = $model;
    }

    /**
     * Insert data of the images
     *
     * @param array $images Data of the images
     *
     * @return void
     */
    public function insert(array $images) {
        $this->model->insert($images);
    }

    /**
     * Returns list of the images
     *
     * @param array|int $id ID
     *
     * @param string    $by Key
     *
     * @return array
     */
    public function getList($id, $by = 'thread') {
        return $this->model->getList($id, $by);
    }

    /**
     * Remove image(s)
     *
     * @param array|string|int $id ID(s)
     * @param string           $by Remove by
     *
     * @return void
     */
    public function remove($id, $by) {
        $this->model->remove($id, $by);
    }

} 