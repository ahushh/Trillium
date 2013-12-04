<?php

/**
 * Part of the Trillium
 *
 * @package Trillium
 */

namespace Trillium\Silex;

use Kilte\Captcha\CaptchaTrait;
use Kilte\SilexPagination\PaginationTrait;
use Kilte\View\ViewTrait;
use Silex\Application\FormTrait;
use Silex\Application\MonologTrait;
use Silex\Application\SecurityTrait;
use Silex\Application\TranslationTrait;
use Silex\Application\UrlGeneratorTrait;
use Trillium\Image\ImageTrait;
use Trillium\ImageBoard\ImageBoardTrait;
use Trillium\User\UserTrait;

/**
 * Application Class
 *
 * @package Trillium\Silex
 */
class Application extends \Silex\Application {

    use CaptchaTrait;
    use FormTrait;
    use ImageBoardTrait;
    use ImageTrait;
    use MonologTrait;
    use PaginationTrait;
    use SecurityTrait;
    use TranslationTrait;
    use UrlGeneratorTrait;
    use UserTrait;
    use ViewTrait;

}