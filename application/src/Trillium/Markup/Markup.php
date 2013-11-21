<?php

/**
 * Part of the Trillium
 *
 * @package Trillium
 */

namespace Trillium\Markup;

/**
 * Markup Class
 *
 * @package Trillium\Markup
 */
class Markup {

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
     * Handle string
     *
     * @param string $string String
     *
     * @return string
     */
    public function handle($string) {
        // Prepare
        $string = htmlspecialchars($string);
        $string = preg_replace('~\r\n?~', "\n", $string);
        $string .= "\n\n";
        $string = preg_replace('~\t~', str_repeat(' ', 4), $string);
        $string = preg_replace('~^[\s]+$~m', '', $string);

        // Blocks
        $string = preg_replace_callback(
            '~^\`([a-z]{0,})\n(.+?)\n\`$~um',
            function ($matches) {
                // TODO: highlight code
                $matches[2] = $this->transformReserved($matches[2]);
                return $matches[1] . 'code:<pre>' . $matches[2] . '</pre>';
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

        $string = preg_replace('~\[(.+?)\]\((https?\:\/\/.+?)\)~us', '<a href="$2" target="_blank">$1</a>', $string);

        return nl2br($string);
    }

    /**
     * Transform reserved symbols
     *
     * @param string $string String
     *
     * @return string
     */
    private function transformReserved($string) {
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