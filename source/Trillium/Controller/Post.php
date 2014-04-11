<?php

/**
 * Part of the Trillium
 *
 * @author  Kilte Leichnam <nwotnbm@gmail.com>
 * @package Trillium
 */

namespace Trillium\Controller;

use Symfony\Component\HttpFoundation\Request;
use Trillium\Service\Imageboard\Exception\ThreadNotFoundException;

/**
 * Post Class
 *
 * @package Trillium\Controller
 */
class Post extends Controller
{

    /**
     * Creates a post
     *
     * @param Request $request A request instance
     * @param int     $thread  Thread ID
     *
     * @return array
     */
    public function create(Request $request, $thread)
    {
        try {
            $thread  = $this->thread->get($thread);
            $message = $request->get('message', '');
            $error   = $this->validator->post($message);
            if (!empty($error)) {
                $result = ['error' => $error, '_status' => 400];
            } else {
                $this->post->create($thread['board'], $thread['id'], $message);
                $result = ['success' => 'Post is created'];
            }
        } catch (ThreadNotFoundException $e) {
            $result = ['error' => $e->getMessage(), '_status' => 404];
        }

        return $result;
    }

    /**
     * Removes a post
     *
     * @param int $id Post ID
     *
     * @return array
     */
    public function remove($id)
    {
        if ($this->post->remove($id) > 0) {
            $result = ['success' => 'Post removed'];
        } else {
            $result = ['error' => 'Post does not exists', '_status' => 404];
        }

        return $result;
    }

}
