<?php

/**
 * Part of the Trillium
 *
 * @package Trillium
 */

namespace Trillium\ImageBoard\Service\Board;

/**
 * Board Class
 *
 * @package Trillium\ImageBoard\Service\Board
 */
class Board {

    /**
     * @var Model Model
     */
    private $model;

    /**
     * @var array Stored data
     */
    private $stored;

    /**
     * @var string Path to the resources drectory
     */
    private $resourcesDir;

    /**
     * @param Model  $model         Model
     * @param string $resourcesDir Path to the resources drectory
     */
    public function __construct(Model $model, $resourcesDir) {
        $this->model = $model;
        $this->stored = [];
        $this->resourcesDir = realpath($resourcesDir) . DS;
    }

    /**
     * Get data of the board
     * Returns null, if board is not exists
     *
     * @param string $name Name of the board
     *
     * @return array|null
     */
    public function get($name) {
        return $this->model->get($name);
    }

    /**
     * Get list of the boards
     *
     * @param boolean $includeHidden Include hidden boards
     *
     * @return array
     */
    public function getList($includeHidden = true) {
        if (!array_key_exists('list', $this->stored)) {
            $this->stored['list']['all'] = $this->model->getBoards();
        }
        if ($includeHidden === false && !isset($this->stored['list']['non_hidden'])) {
            $this->stored['list']['non_hidden'] = [];
            foreach ($this->stored['list']['all'] as $board) {
                if ($board['hidden']) {
                    continue;
                }
                $this->stored['list']['non_hidden'][] = $board;
            }
        }
        return $includeHidden ? $this->stored['list']['all'] : $this->stored['list']['non_hidden'];
    }

    /**
     * Check board for exists
     *
     * @param string $name Name of the board
     *
     * @return boolean
     */
    public function isExists($name) {
        return $this->model->isExists($name);
    }

    /**
     * Remove board
     *
     * @param string $name Name of the board
     *
     * @return void
     */
    public function remove($name) {
        $this->model->removeBoard($name);
        $directory = $this->resourcesDir . $name;
        $entries = array_diff(scandir($directory), ['.', '..']);
        array_map(
            function ($entry) use ($directory) {
                unlink($directory . DS . $entry);
            },
            $entries
        );
        rmdir($directory);
    }

    /**
     * Perform check and save data of the board
     * Returns array width errors, if occured, else returns true
     *
     * @param array    $data      Data
     * @param array    $defaults  Defaults
     *
     * @return array|boolean
     */
    public function manage(array $data, array $defaults = []) {
        $new = empty($defaults);
        $save = [];
        $error = [];
        if ($new) {
            $defaults = [
                'name'             => '',    // Name
                'summary'          => '',    // Summary
                'bump_limit'       => 500,   // Bump limit (0 - unlimited)
                'max_file_size'    => 1024,  // Max file size in KiB
                'images_per_post'  => 1,     // Number of the images per post
                'thumb_width'      => 64,    // Width of the thumbnail in the pixels
                'pages'            => 1,     // Number of the pages in the board
                'threads_per_page' => 1,     // Threads per page
                'ip_seconds_limit' => 10,    // Limit for create posts by one IP in seconds (0 - unlimited)
                'hidden'           => false, // Is board hidden?
                'captcha'          => true,  // Enable/Disable captcha
                'blotter'          => '',    // Blotter
            ];
            $save['name'] = isset($data['name']) ? trim($data['name']) : '';
            if (strlen($save['name']) < 1 || strlen($save['name']) > 10) {
                $error['name'] = ['The length of the value must be in the range of %s to %s characters', 1, 10];
            } elseif (preg_match('~[^a-z\d]~i', $save['name'])) {
                $error['name'] = 'Value must contain only latin characters and numbers';
            } elseif ($this->isExists($save['name'])) {
                $error['name'] = 'Board already exists';
            }
        } else {
            $save['name'] = $defaults['name'];
        }
        $save['summary'] = isset($data['summary']) ? trim($data['summary']) : $defaults['summary'];
        if (strlen($save['summary']) > 200) {
            $error['summary'] = ['The length of the value must not exceed %s characters', 200];
        }
        $save['bump_limit'] = isset($data['bump_limit']) ? (int) $data['bump_limit'] : $defaults['bump_limit'];
        if ($save['bump_limit'] !== 0 && ($save['bump_limit'] < 100 || $save['bump_limit'] > 999)) {
            $error['bump_limit'] = ['The value must be between %s and %s', 100, 999];
        }
        $save['max_file_size'] = isset($data['max_file_size']) ? (int) $data['max_file_size'] : $defaults['max_file_size'];
        if ($save['max_file_size'] > 20480 || $save['max_file_size'] < 1024) {
            $error['max_file_size'] = ['The value must be between %s and %s', 1024, 20480];
        }
        $save['images_per_post'] = isset($data['images_per_post']) ? (int) $data['images_per_post'] : $defaults['images_per_post'];
        if ($save['images_per_post'] > 10 || $save['images_per_post'] < 1) {
            $error['images_per_post'] = ['The value must be between %s and %s', 1, 10];
        }
        $save['thumb_width'] = isset($data['thumb_width']) ? (int) $data['thumb_width'] : $defaults['thumb_width'];
        if ($save['thumb_width'] < 64 || $save['thumb_width'] > 999) {
            $error['thumb_width'] = ['The value must be between %s and %s', 64, 999];
        }
        $save['pages'] = isset($data['pages']) ? (int) $data['pages'] : $defaults['pages'];
        if ($save['pages'] < 1 || $save['pages'] > 99) {
            $error['pages'] = ['The value must be between %s and %s', 1, 99];
        }
        $save['threads_per_page'] = isset($data['threads_per_page']) ? (int) $data['threads_per_page'] : $defaults['threads_per_page'];
        if ($save['threads_per_page'] < 1 || $save['threads_per_page'] > 99) {
            $error['threads_per_page'] = ['The value must be between %s and %s', 1, 99];
        }
        $save['ip_seconds_limit'] = isset($data['ip_seconds_limit']) ? (int) $data['ip_seconds_limit'] : $defaults['ip_seconds_limit'];
        if ($save['ip_seconds_limit'] < 0 || $save['ip_seconds_limit'] > 300) {
            $error['ip_seconds_limit'] = ['The value must be between %s and %s', 0, 300];
        }
        $save['blotter'] = isset($data['blotter']) ? trim($data['blotter']) : $defaults['bloter'];
        if (strlen($save['blotter']) > 800) {
            $error['blotter'] = ['The length of the value must not exceed %s characters', 800];
        }
        $save['blotter'] = preg_replace('~\r\n?~', "\n", $save['blotter']);
        $save['hidden'] = (int) isset($data['hidden']);
        $save['captcha'] = (int) isset($data['captcha']);

        if (empty($error)) {
            $save['max_file_size'] = $save['max_file_size'] * 1024;
            $this->model->saveBoard($save);
            if ($new) {
                mkdir($this->resourcesDir . $save['name']);
            }
            return true;
        }
        return ['error' => $error, 'data' => $data];
    }

}