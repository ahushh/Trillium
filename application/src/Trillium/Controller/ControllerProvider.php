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
class ControllerProvider implements ControllerProviderInterface
{
    /**
     * Returns routes to connect to the given application.
     *
     * @param Application $app An Application instance
     *
     * @return ControllerCollection A ControllerCollection instance
     */
    public function connect(Application $app)
    {
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
            'controllers.panel.imageboard'  => 'Panel\ImageBoard',
            'controllers.imageboard.board'  => 'Imageboard\Board',
            'controllers.imageboard.thread' => 'Imageboard\Thread',
            'controllers.imageboard.ajax'   => 'Imageboard\Ajax',
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
        /** Imageboard */
        /** Boards */
        $collection->get('panel/imageboard/board/list', 'controllers.panel.imageboard:boardList')
            ->bind('panel.imageboard.board.list');
        $collection->match('panel/imageboard/board/manage/{name}', 'controllers.panel.imageboard:boardManage')
            ->bind('panel.imageboard.board.manage')
            ->value('name', '');
        $collection->get('panel/imageboard/board/remove/{name}', 'controllers.panel.imageboard:boardRemove')
            ->bind('panel.imageboard.board.remove');
        /** Threads */
        $collection->get('panel/imageboard/thread/remove/{id}', 'controllers.panel.imageboard:threadRemove')
            ->bind('panel.imageboard.thread.remove');
        $collection->post('panel/imageboard/thread/remove', 'controllers.panel.imageboard:threadRemove')
            ->bind('panel.imageboard.thread.mass_remove');
        $collection->match('panel/imageboard/thread/manage/{action}/{id}', 'controllers.panel.imageboard:threadManage')
            ->bind('panel.imageboard.thread.manage')
            ->assert('id', '[\d]+');
        $collection->match('panel/imageboard/thread/rename/{id}', 'controllers.panel.imageboard:threadRename')
            ->bind('panel.imageboard.thread.rename')
            ->assert('id', '[\d]+');
        $collection->match('panel/imageboard/thread/move/{id}', 'controllers.panel.imageboard:threadMove')
            ->bind('panel.imageboard.thread.move')
            ->assert('id', '[\d]+');
        /** Posts */
        $collection->match('panel/imageboard/post/remove/{id}', 'controllers.panel.imageboard:postRemove')
            ->bind('panel.imageboard.post.remove');
        /** Images */
        $collection->get('panel/imageboard/image/remove/{id}', 'controllers.panel.imageboard:imageRemove')
            ->bind('panel.imageboard.image.remove');

        /** Imageboard */
        $collection->match('board/{name}/{page}', 'controllers.imageboard.board:view')
            ->bind('imageboard.board.view')
            ->value('page', 1);
        $collection->match('thread/{id}', 'controllers.imageboard.thread:view')
            ->bind('imageboard.thread.view')
            ->assert('id', '[\d]+');
        $collection->get('ajax/post/{id}', 'controllers.imageboard.ajax:post')
            ->bind('imageboard.ajax.post')
            ->assert('id', '[\d]+');


        return $collection;
    }

}
