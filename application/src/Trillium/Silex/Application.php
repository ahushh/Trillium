<?php

/**
 * Part of the Trillium
 *
 * @package Trillium
 */

namespace Trillium\Silex;

use Kilte\View\ViewTrait;
use Silex\Application\FormTrait;
use Silex\Application\MonologTrait;
use Silex\Application\SecurityTrait;
use Silex\Application\TranslationTrait;
use Silex\Application\UrlGeneratorTrait;
use Trillium\User\UserTrait;

/**
 * Application Class
 *
 * @package Trillium\Silex
 */
class Application extends \Silex\Application {

    use FormTrait;
    use MonologTrait;
    use SecurityTrait;
    use TranslationTrait;
    use UrlGeneratorTrait;
    use UserTrait;
    use ViewTrait;

}