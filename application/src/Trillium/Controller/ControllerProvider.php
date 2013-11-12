<?php

/**
 * Part of the Trillium
 *
 * @package Trillium
 */

namespace Trillium\Controller;

use Silex\Application;
use Silex\ControllerCollection;
use Silex\ControllerProviderInterface;

/**
 * ControllerProvider Class
 *
 * Provider for the controllers
 *
 * @package Trillium\Controller
 */
class ControllerProvider implements ControllerProviderInterface {

    /**
     * Returns routes to connect to the given application.
     *
     * @param Application $app An Application instance
     *
     * @return ControllerCollection A ControllerCollection instance
     */
    public function connect(Application $app) {
        $controllerFactory = function ($className) use ($app) {
            $className = '\Application\Controller\\' . $className;
            $controller = new $className($app);
            if (!($controller instanceof Controller)) {
                throw new \RuntimeException(sprintf('Controller %s should have instance of \trillium\Controller', $className));
            }
            return $controller;
        };

        $controllers = [
            'controllers.trillium'          => 'Trillium',
            'controllers.panel'             => 'Panel',
            'controllers.panel.users'       => 'Panel\Users',
            'controllers.panel.boards'      => 'Panel\Boards',
            'controllers.imageboard.board'  => 'Imageboard\Board',
            'controllers.imageboard.thread' => 'Imageboard\Thread',
        ];
        foreach ($controllers as $key => $className) {
            $app[$key] = $app->share(function () use ($controllerFactory, $className) {
                return $controllerFactory($className);
            });
        }

        /** @var ControllerCollection $collection */
        $collection = $app['controllers_factory'];

        $collection->get('', 'controllers.trillium:mainpage');
        $collection->get('login', 'controllers.trillium:login')
            ->bind('login');

        /** Control panel */
        $collection->get('panel', 'controllers.panel:menu')
            ->bind('panel');
        /** Mainpage Editor */
        $collection->match('panel/mainpage', 'controllers.panel:mainpage')
            ->bind('panel.mainpage');
        /** Users */
        $collection->get('panel/users', 'controllers.panel.users:usersList')
            ->bind('panel.users');
        $collection->match('panel/users/manage/{name}', 'controllers.panel.users:manage')
            ->bind('panel.users.manage')
            ->value('name', '');
        $collection->get('panel/users/remove/{name}', 'controllers.panel.users:remove')
            ->bind('panel.users.remove');
        $collection->match('panel/change.password', 'controllers.panel.users:changePassword')
            ->bind('panel.users.change.password');
        /** Boards */
        $collection->get('panel/boards', 'controllers.panel.boards:boardsList')
            ->bind('panel.boards');
        $collection->match('panel/boards/manage/{name}', 'controllers.panel.boards:manage')
            ->bind('panel.boards.manage')
            ->value('name', '');
        $collection->get('panel/boards/remove/{name}', 'controllers.panel.boards:remove')
            ->bind('panel.boards.remove');

        /** Imageboard */
        $collection->match('board/{name}', 'controllers.imageboard.board:view')
            ->bind('imageboard.board.view');
        $collection->match('thread/{id}', 'controllers.imageboard.thread:view')
            ->bind('imageboard.thread.view')
            ->assert('id', '[\d]+');

        return $collection;
    }

}