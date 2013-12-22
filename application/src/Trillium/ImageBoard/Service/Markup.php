<?php

/**
 * Part of the Trillium
 *
 * @package Trillium
 */

namespace Trillium\ImageBoard\Service;

use FSHL\Highlighter;

/**
 * Markup Class
 *
 * @package Trillium\ImageBoard\Service
 */
class Markup
{
    /**
     * @var array Inline rules
     */
    private $inline = [
        '~\`\{(.+?)\}\`~u' => '<code>$1</code>',
        '~\*\*(.+?)\*\*~u' => '<b>$1</b>',
        '~\*(.+?)\*~u'     => '<i>$1</i>',
        '~\%\%(.+?)\%\%~u' => '<span class="spoiler">$1</span>',
        '~\~(.+?)\~~u'     => '<span style="text-decoration: line-through;">$1</span>',
        '~\_(.+?)\_~u'     => '<span style="text-decoration: underline;">$1</span>',
    ];

    /**
     * @var \FSHL\Highlighter Code highlighter
     */
    private $highlighter;

    /**
     * @var array List of the posts IDs
     */
    private $posts = [];

    /**
     * Create instance
     *
     * @param Highlighter $highlighter Code Highlighter
     *
     * @return Markup
     */
    public function __construct(Highlighter $highlighter)
    {
        $this->highlighter = $highlighter;
    }

    /**
     * Set list of posts identifiers
     *
     * @param array $posts Posts IDs
     *
     * @return void
     */
    public function setPosts(array $posts)
    {
        $this->posts = [];
        foreach ($posts as $post) {
            $this->posts[(int) $post['id']] = $post;
        }
    }

    /**
     * Handle string
     *
     * @param string      $string String
     * @param int|null    $pid    ID of the current post
     * @param string|null $author ID of the post's author
     *
     * @return string
     */
    public function handle($string, $pid = null, $author = null)
    {
        // Prepare
        $string = htmlspecialchars($string);
        $string = preg_replace('~\r\n?~', "\n", $string);
        $string = preg_replace('~\t~', str_repeat(' ', 4), $string);
        $string = preg_replace('~^[\s]+$~m', '', $string);

        // Answers
        $string = preg_replace_callback(
            '~&gt;&gt;([\d]+)~u',
            function ($matches) use ($pid) {
                if (array_key_exists($matches[1], $this->posts)) {

                    return '<a '
                            . ($pid === null ? : 'rel="' . $pid . '"')
                            . ' href="#' . $matches[1] . '"'
                            . ' class="answer"'
                            . ' onclick="previewPost.show(event, \'' . $matches[1] . '\')"'
                        . '>'
                        . $matches[0]
                        . '</a>';
                } else {

                    return $matches[0];
                }
            },
            $string
        );

        // Blocks
        $string = preg_replace_callback(
            '~^\`([a-z]{0,})\n(.+?)\n\`$~ums',
            function ($matches) {
                $lexer = '\FSHL\Lexer\\' . ucwords($matches[1]);
                if (class_exists($lexer)) {
                    $matches[2] = htmlspecialchars_decode($matches[2]);
                    $matches[2] = $this->highlighter->highlight($matches[2], new $lexer);
                }
                $matches[2] = $this->transformReserved($matches[2]);

                return '<pre>' . $matches[2] . '</pre>';
            },
            $string
        );
        $string = preg_replace('~^\&gt\;(.+?)$~um', '<blockquote>$1</blockquote>', $string);

        // Headers
        $string = preg_replace_callback(
            '~^(\#{1,6})(.+?)$~m',
            function ($matches) {
                $level = strlen($matches[1]);

                return '<h' . $level . '>' . $matches[2] . '</h' . $level . '>';
            },
            $string
        );

        // Inline
        foreach ($this->inline as $pattern => $replace) {
            $string = preg_replace_callback(
                $pattern,
                function ($matches) use ($replace) {
                    $matches[1] = $this->transformReserved($matches[1]);

                    return str_replace('$1', $matches[1], $replace);
                },
                $string
            );
        }

        $string = preg_replace('~\[(.+?)\]\((https?\:\/\/.+?)\)~us', '<a rel="noreferrer" href="$2" target="_blank">$1</a>', $string);

        // Prooflabes
        $string = preg_replace_callback(
            '~\%\%(\d+)~u',
            function ($matches) use ($author) {
                $isAuthor = (array_key_exists($matches[1], $this->posts) && $this->posts[$matches[1]]['author'] == $author);

                return '<span class="prooflabel_' . ($isAuthor ? 'yes' : 'no') . '">'
                    . '<a '
                        . ' href="#' . $matches[1] . '"'
                        . ' class="answer"'
                        . ' onclick="previewPost.show(event, \'' . $matches[1] . '\')"'
                    . '>'
                    . '##' . $matches[1]
                    . '</a></span>';
            },
            $string
        );



        return nl2br($string);
    }

    /**
     * Transform reserved symbols
     *
     * @param string $string String
     *
     * @return string
     */
    private function transformReserved($string)
    {
        return strtr($string, [
            '*' => '&#42;',
            '%' => '&#37;',
            '~' => '&#126;',
            '_' => '&#95;',
            '`' => '&#96;',
            '[' => '&#91;',
            ']' => '&#93;',
            '(' => '&#40;',
            ')' => '&#41;',
        ]);
    }

}
