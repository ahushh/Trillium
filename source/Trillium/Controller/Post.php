<?php

/**
 * Part of the Trillium
 *
 * @author  Kilte Leichnam <nwotnbm@gmail.com>
 * @package Trillium
 */

namespace Trillium\Controller;

use Kilte\AccountManager\Exception\AccessDeniedException;
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
            try {
                $this->userController->getUser();
            } catch (AccessDeniedException $e) {
                if (!$this->container['captcha.test']($request->get('captcha', ''))) {
                    $error[] = 'Wrong captcha';
                }
            }
            if (!empty($error)) {
                $result = ['error' => $error, '_status' => 400];
            } else {
                $this->post->create($thread['board'], $thread['id'], $message, time());
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

    /**
     * Returns the list of the posts
     *
     * @param string $thread Thread ID
     *
     * @return array
     */
    public function listing($thread)
    {
        if (!$this->thread->isExists($thread)) {
            $result = ['error' => 'Thread does not exists', '_status' => 404];
        } else {
            $result = array_map(
                function ($post) {
                    $post['time']    = $this->date->format($post['time']);
                    $post['message'] = $this->markdown->render(htmlspecialchars($post['message']));
                    $post['message'] = str_replace('<a href="javascript', '<a href="', $post['message']);

                    return $post;
                },
                $this->post->listing($thread)
            );
        }

        return $result;
    }

}
