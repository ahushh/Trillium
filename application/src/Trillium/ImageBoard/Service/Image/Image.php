<?php

/**
 * Part of the Trillium
 *
 * @package Trillium
 */

namespace Trillium\ImageBoard\Service\Image;

use Trillium\Exception\UnexpectedValueException;
use Trillium\Image\ImageService;
use Trillium\ImageBoard\Exception\ServiceImageException;

/**
 * Image Class
 *
 * @package Trillium\ImageBoard\Service\Image
 */
class Image
{
    /**
     * Name of the ID key
     */
    const ID = 'id';

    /**
     * Name of the board key
     */
    const BOARD = 'board';

    /**
     * Name of the thread key
     */
    const THREAD = 'thread';

    /**
     * Name of the post key
     */
    const POST = 'post';

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
    public function __construct(Model $model, $resourcesDir)
    {
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
    public function insert(array $images)
    {
        $this->model->insert($images);
    }

    /**
     * Returns list of the images
     *
     * @param array|int $id ID
     * @param string    $by Key
     *
     * @throws UnexpectedValueException
     * @return array
     */
    public function getList($id, $by = self::THREAD)
    {
        $expected = [self::BOARD, self::THREAD, self::POST];
        if (!in_array($by, $expected)) {
            throw new UnexpectedValueException('by', implode(', ', $expected));
        }
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
     * @throws UnexpectedValueException
     * @return void
     */
    public function remove($id, $by)
    {
        $expected = [self::ID, self::BOARD, self::THREAD, self::POST];
        if (!in_array($by, $expected)) {
            throw new UnexpectedValueException('by', implode(', ',$expected));
        }
        $this->model->remove($by, $id);
    }

    /**
     * Get data of the image
     * Returns null, if image is not exists
     *
     * @param int $id ID of the image
     *
     * @return array|null
     */
    public function get($id)
    {
        return $this->model->get($id);
    }

    /**
     * Remove images from resources directory
     *
     * @param array $images Data of the images
     *
     * @return void
     */
    public function removeFiles(array $images)
    {
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

    /**
     * Perfrom check images
     *
     * @param array $images    List of the images
     * @param int   $maxImages Max number of the images
     * @param int   $maxSize   Max file size
     *
     * @throws ServiceImageException
     * @return array
     */
    public function performCheck(array $images, $maxImages, $maxSize)
    {
        $tmpImages = [];
        $i = 0;
        foreach ($images as $key => $values) {
            foreach ($values as $value) {
                if (($key == 'error' && $value == 4) || empty($value)) {
                    continue;
                }
                $tmpImages[$i][$key] = $value;
                $i++;
            }
            $i = 0;
        }
        if (sizeof($tmpImages) > $maxImages) {
            $error = ['The number of images should be no more than %s', $maxImages];
        } else {
            $images = [];
            foreach ($tmpImages as $image) {
                if ($image['size'] > $maxSize) {
                    $error = ['File size should not exceed %s', $maxSize / 1024 . ' Kb'];
                } elseif (!in_array($image['type'], ['image/png', 'image/gif', 'image/jpeg'])) {
                    $error = 'Illegal file type';
                } else {
                    $ext = explode('.', $image['name']);
                    $ext = strtolower(end($ext));
                    if (!in_array($ext, ['png', 'gif', 'jpg', 'jpeg'])) {
                        $error = 'Illegal file type';
                    }
                }
                if (!isset($error)) {
                    try {
                        $images[] = [
                            'service'  => new ImageService($image['tmp_name']),
                            'tmp_name' => $image['tmp_name'],
                            'size'     => $image['size'],
                        ];
                    } catch (\RuntimeException $e) {
                        $error = $e->getMessage();
                        break;
                    }
                } else {
                    break;
                }
            }
        }
        if (isset($error)) {
            throw new ServiceImageException($error);
        }

        return $images;
    }

    /**
     * Upload images and save their data to the database
     *
     * @param array  $images     List of the images
     * @param string $board      Name of the board
     * @param int    $thread     ID of the thread
     * @param int    $post       ID of the post
     * @param int    $thumbWidth Width of the thumbnail
     *
     * @return void
     */
    public function upload(array $images, $board, $thread, $post, $thumbWidth)
    {
        $imagesData = [];
        $filePath = $this->resourcesDir . $board . DS;
        foreach ($images as $image) {
            /** @var \Trillium\Image\ImageService $imageService */
            $imageService = $image['service'];
            $fileName = md5(microtime(true) . $image['tmp_name'] . rand(1000, 9999));
            if ($imageService->width() > $thumbWidth) {
                $thumb = $imageService->resizeWidth($thumbWidth);
            } else {
                $thumb = $imageService->resource();
            }
            if ($imageService->type() === IMAGETYPE_GIF) {
                copy($image['tmp_name'], $filePath . $fileName . '.gif');
            } else {
                $imageService->save($filePath . $fileName);
            }
            $imageService->save($filePath . $fileName . '_small', $thumb);
            $imagesData[] = [
                'board'    => $board,
                'thread'   => $thread,
                'post'     => $post,
                'name'     => $fileName,
                'ext'      => $imageService->extension(),
                'width'    => $imageService->width(),
                'height'   => $imageService->height(),
                'size'     => $image['size'],
            ];
        }
        $this->insert($imagesData);
    }

    /**
     * Move images of the thread to other board
     *
     * @param int    $thread   ID of the thread
     * @param string $newBoard Name of the new board
     *
     * @return void
     */
    public function move($thread, $newBoard)
    {
        $list = [];
        foreach ($this->getList($thread, self::THREAD) as $images) {
            $list = array_merge($list, $images);
        }
        if (!empty($list)) {
            foreach ($list as $image) {
                $path = $this->resourcesDir . '%s' . DS . $image['name'] . '%s.' . $image['ext'];
                rename(sprintf($path, $image['board'], ''), sprintf($path, $newBoard, ''));
                rename(sprintf($path, $image['board'], '_small'), sprintf($path, $newBoard, '_small'));
            }
            $this->model->update(['board' => $newBoard], 'thread', $thread);
        }
    }

}
