<?php

/**
 * Part of the Trillium
 *
 * @package Trillium
 */

namespace Trillium\ImageBoard\Service;

use Kilte\Captcha\Captcha;

/**
 * Message Class
 *
 * @package Trillium\ImageBoard\Service
 */
class Message {

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
     * @param ImageBoard             $aib ImageBoard service
     * @param \Kilte\Captcha\Captcha $captcha
     *
     * @return Message
     */
    public function __construct(ImageBoard $aib, Captcha $captcha) {
        $this->aib = $aib;
        $this->captcha = $captcha;
    }

    /**
     * Send message
     * Returns ID of the thread, if message created, else returns array with errors
     *
     * @param array      $board      Data of the board
     * @param array      $data       Data of the new message
     * @param int        $ip         IP address of the poster
     * @param string     $userID     ID of the user
     * @param array|null $thread     Data of the thread for answer
     * @param int|null   $totalPosts Number of posts in the thread
     *
     * @return array|int
     */
    public function send(array $board, array $data, $ip, $userID, array $thread = null, $totalPosts = null) {
        $newThread = $thread === null;
        $error = [];
        if (!empty($data)) {
            $result = $this->check($data, $newThread, $ip, $board['ip_seconds_limit'], (boolean) $board['captcha']);
            if (isset($result['error'])) {
                $error = $result['error'];
            } else {
                if (!empty($data['images']) && is_array($data['images'])) {
                    $check = $this->aib->image()->performCheck($data['images'], $board['images_per_post'], $board['max_file_size']);
                    isset($check['error']) ? $error['images'] = $check['error'] : $images = $check['images'];
                }
            }
            if (empty($error)) {
                $inBumpLimit = $totalPosts !== null ? ($board['bump_limit'] < $totalPosts) : false;
                $created = $this->create($board['name'], $newThread ? null : (int) $thread['id'], $ip, $userID, $result['data'], $inBumpLimit);
                if (!empty($images)) {
                    $this->aib->image()->upload($images, $board['name'], $created['thread'], $created['post'], (int) $board['thumb_width']);
                }
                if ($newThread) {
                    $this->aib->removeRedundantThreads($board['name'], $board['pages'] * $board['threads_per_page']);
                }
                return $created['thread'];
            }
        }
        return $error;
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
     * @return array
     */
    protected function check(array $data, $newThread, $ip, $ipSecondsLimit, $captcha) {
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
            if (empty($save['theme'])) {
                $error['theme'] = 'The value could not be empty';
            } elseif (strlen($save['theme']) > 200) {
                $error['theme'] = ['The length of the value must not exceed %s characters', 200];
            }
        } else {
            $save['sage'] = isset($data['sage']);
        }
        // Message
        $save['text'] = !empty($data['text']) ? trim($data['text']) : '';
        if (empty($save['text'])) {
            $error['text'] = 'The value could not be empty';
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
        return !empty($error) ? ['error' => $error] : ['data' => $save];
    }

    /**
     * Save data of the new message in the database
     * Returns identifiers of the thread and of the post
     *
     * @param string   $board       Name of the board
     * @param int|null $thread      ID of the thread
     * @param int      $ip          IP address of the poster in the long format
     * @param string   $userID      ID of the user
     * @param array    $data        Data of the new message
     * @param boolean  $inBumpLimit Number of the posts in thread
     *
     * @return array
     */
    protected function create($board, $thread, $ip, $userID, array $data, $inBumpLimit = false) {
        if ($thread === null) {
            $thread = $this->aib->thread()->create($board, $data['theme']);
            $newThread = true;
        } else {
            $newThread = false;
        }
        $userAgent = isset($_SERVER['HTTP_USER_AGENT']) ? trim(substr($_SERVER['HTTP_USER_AGENT'], 0, 150)) : '';
        $bump = isset($data['sage']) && $data['sage'] === true ? false : true;
        $postID = $this->aib->post()->create([
            'board'      => $board,
            'thread'     => $thread,
            'text'       => $data['text'],
            'video'      => isset($data['video']) ? $data['video'] : '',
            'sage'       => !$bump ? 1 : 0,
            'ip'         => $ip,
            'user_agent' => $userAgent,
            'time'       => time(),
            'author'     => $userID,
        ]);
        if ($inBumpLimit !== false) {
            $bump = false;
        }
        $this->aib->thread()->bump($thread, $newThread ? $postID : null, $bump);
        return ['thread' => $thread, 'post' => $postID];
    }

}