<?php

/**
 * Part of the Trillium
 *
 * @package Trillium
 */

namespace Trillium\ImageBoard\Service;

use Kilte\Captcha\Captcha;
use Trillium\ImageBoard\Exception\ServiceImageException;
use Trillium\ImageBoard\Exception\ServiceMessageException;

/**
 * Message Class
 *
 * @package Trillium\ImageBoard\Service
 */
class Message
{
    /**
     * Empty string error message
     */
    const ERROR_EMPTY_STRING = 'The value could not be empty';

    /**
     * @var ImageBoard ImageBoard Service
     */
    private $aib;

    /**
     * @var Captcha Captcha instance
     */
    private $captcha;

    /**
     * Create Message instance
     *
     * @param ImageBoard             $aib     ImageBoard service
     * @param \Kilte\Captcha\Captcha $captcha
     *
     * @return Message
     */
    public function __construct(ImageBoard $aib, Captcha $captcha)
    {
        $this->aib     = $aib;
        $this->captcha = $captcha;
    }

    /**
     * Send message
     * Returns ID of the thread, if message created
     *
     * @param array      $board      Data of the board
     * @param array      $data       Data of the new message
     * @param int        $ip         IP address of the poster
     * @param string     $userID     ID of the user
     * @param array|null $thread     Data of the thread for answer
     * @param int|null   $totalPosts Number of posts in the thread
     *
     * @throws ServiceMessageException
     * @return int|null
     */
    public function send(array $board, array $data, $ip, $userID, array $thread = null, $totalPosts = null)
    {
        if (!empty($data)) {
            $newThread = $thread === null;
            $error = [];
            $result = [];
            try {
                $result = $this->check($data, $newThread, $ip, $board['ip_seconds_limit'], (boolean) $board['captcha']);
            } catch (ServiceMessageException $e) {
                $error = $e->getMessage();
            }
            if (!empty($data['images']) && is_array($data['images']) && $board['images_per_post'] > 0) {
                try {
                    $images = $this->aib->image()->performCheck($data['images'], $board['images_per_post'], $board['max_file_size']);
                } catch (ServiceImageException $e) {
                    $error['images'] = $e->getMessage();
                }
            }
            // Allow empty message, if attaches exists
            if (isset($error['text']) && ($error['text'] === self::ERROR_EMPTY_STRING) && !empty($images)) {
                unset($error['text']);
            }
            if (!empty($error)) {
                throw new ServiceMessageException($error);
            }
            // Define bump
            if (is_array($thread)) {
                if ($thread['auto_sage_bump'] == 1) { // Autosage
                    $bump = false;
                } elseif ($thread['auto_sage_bump'] == 2) { // Autobump
                    $bump = true;
                } else {
                    if ($board['bump_limit'] != 0 && $totalPosts !== null) { //Bump limit
                        $bump = $totalPosts > $board['bump_limit'] ? false : true;
                    } else {
                        $bump = !$result['sage']; // Sage
                    }
                }
            } else {
                $bump = true; // New thread
            }
            $created = $this->create($board['name'], ($newThread ? null : (int) $thread['id']), $ip, $userID, $result, $bump);
            if (!empty($images)) {
                $this->aib->image()->upload($images, $board['name'], $created['thread'], $created['post'], (int) $board['thumb_width']);
            }
            if ($newThread) {
                $this->aib->removeRedundantThreads($board['name'], $board['pages'] * $board['threads_per_page']);
            }

            return $created['thread'];
        }

        return null;
    }

    /**
     * Check message data
     *
     * @param array   $data           Data
     * @param boolean $newThread      Is new thread?
     * @param int     $ip             IP address of the poster
     * @param int     $ipSecondsLimit Limit for IP in seconds (0 - unlimited)
     * @param boolean $captcha        Check captha
     *
     * @throws ServiceMessageException
     * @return array
     */
    protected function check(array $data, $newThread, $ip, $ipSecondsLimit, $captcha)
    {
        $error = [];
        if ($ipSecondsLimit > 0) {
            $lastPostTime = $this->aib->post()->timeOfLastIP((int) $ip);
            if ($lastPostTime !== null && (time() - $ipSecondsLimit < $lastPostTime)) {
                $error['text'] = ['Too many requests from your IP. Wait %s seconds.', abs(time() - $lastPostTime - $ipSecondsLimit)];
            }
        }
        if ($captcha) {
            if (!$this->captcha->performCheck(isset($data['captcha']) ? $data['captcha'] : null)) {
                $error['captcha'] = 'The value is incorrect';
            }
        }
        $save = [];
        if ($newThread) {
            // Theme of the thread
            $save['theme'] = !empty($data['theme']) ? trim($data['theme']) : '';
            $result = $this->aib->thread()->checkTheme($save['theme']);
            if ($result !== null) {
                $error['theme'] = $result;
            }
            unset($result);
        } else {
            $save['sage'] = isset($data['sage']);
        }
        // Message
        $save['text'] = !empty($data['text']) ? trim($data['text']) : '';
        if (empty($save['text'])) {
            $error['text'] = self::ERROR_EMPTY_STRING;
        } elseif (strlen($save['text']) > 8000) {
            $error['text'] = ['The length of the value must not exceed %s characters', 8000];
        }
        // Video
        $video = !empty($data['video']) ? trim($data['video']) : '';
        if (!empty($video)) {
            $videoSubject = strtr($video, [
                'http://www.youtube.com' => 'youtube-com',
                'http://m.youtube.com'   => 'youtube-com',
                'http://youtu.be/'       => 'youtube-com/watch?v=',
            ]);
            preg_match('!youtube\-com\/watch\?v=([a-z\d\-_]+)([^\s|\[]+)?!si', $videoSubject, $videoMatches);
            if (isset($videoMatches[1])) {
                $save['video'] = 'youtube.com/embed/' . $videoMatches[1];
            } else {
                $error['video'] = 'Wrong video URL given';
            }
        }
        if (empty($save['text']) && !empty($save['video'])) {
            unset($error['text']);
        }
        if (!empty($error)) {
            throw new ServiceMessageException($error);
        }

        return $save;
    }

    /**
     * Save data of the new message in the database
     * Returns identifiers of the thread and of the post
     *
     * @param string   $board  Name of the board
     * @param int|null $thread ID of the thread
     * @param int      $ip     IP address of the poster in the long format
     * @param string   $userID ID of the user
     * @param array    $data   Data of the new message
     * @param boolean  $bump   Bump thread?
     *
     * @return array
     */
    protected function create($board, $thread, $ip, $userID, array $data, $bump)
    {
        if ($thread === null) {
            $thread = $this->aib->thread()->create($board, $data['theme']);
            $newThread = true;
        } else {
            $newThread = false;
        }
        $userAgent = isset($_SERVER['HTTP_USER_AGENT']) ? trim(substr($_SERVER['HTTP_USER_AGENT'], 0, 150)) : '';
        $postID = $this->aib->post()->create([
            'board'      => $board,
            'thread'     => $thread,
            'text'       => isset($data['text']) ? $data['text'] : '',
            'video'      => isset($data['video']) ? $data['video'] : '',
            'sage'       => $data['sage'] ? 1 : 0,
            'ip'         => $ip,
            'user_agent' => $userAgent,
            'time'       => time(),
            'author'     => $userID,
        ]);
        $this->aib->thread()->bump($thread, $newThread ? $postID : null, $bump);

        return ['thread' => $thread, 'post' => $postID];
    }

}
