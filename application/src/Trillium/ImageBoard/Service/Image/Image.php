<?php

/**
 * Part of the Trillium
 *
 * @package Trillium
 */

namespace Trillium\ImageBoard\Service\Image;

/**
 * Image Class
 *
 * @package Trillium\ImageBoard\Service\Image
 */
class Image {

    /**
     * @var Model Model
     */
    private $model;

    /**
     * @var string
     */
    private $resourcesDir;

    /**
     * Create Image instance
     *
     * @param Model  $model        Model
     * @param string $resourcesDir Path to the resources directory
     *
     * @return Image
     */
    public function __construct(Model $model, $resourcesDir) {
        $this->model = $model;
        $this->resourcesDir = realpath($resourcesDir) . DS;
    }

    /**
     * Insert data of the images
     *
     * @param array $images
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
     * @param string    $by Key
     *
     * @throws \UnexpectedValueException
     * @throws \InvalidArgumentException
     * @return array
     */
    public function getList($id, $by = 'thread') {
        $list = $this->model->getImages($id, $by);
        $return = [];
        foreach ($list as $image) {
            $return[$image['post']][] = $image;
        }
        return $return;
    }

    /**
     * Remove image(s)
     *
     * @param array|string|int $id ID(s)
     * @param string           $by Remove by
     *
     * @throws \UnexpectedValueException
     * @return void
     */
    public function remove($id, $by) {
        $this->model->remove($by, $id);
    }

    /**
     * Get data of the image
     * Returns null, if image is not exists
     *
     * @param int $id ID of the image
     *
     * @throws \InvalidArgumentException
     * @return array|null
     */
    public function get($id) {
        return $this->model->get($id);
    }

    /**
     * Remove images from resources directory
     *
     * @param array $images Data of the images
     *
     * @return void
     */
    public function removeFiles(array $images) {
        array_map(
            function ($images) {
                foreach ($images as $image) {
                    $image = $this->resourcesDir . $image['board'] . DS . $image['name'] . '%s.' . $image['ext'];
                    if (is_file(sprintf($image, ''))) {
                        unlink(sprintf($image, ''));
                    }
                    if (is_file(sprintf($image, '_small'))) {
                        unlink(sprintf($image, '_small'));
                    }
                }
            },
            $images
        );
    }

}