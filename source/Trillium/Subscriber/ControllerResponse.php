<?php

/**
 * Part of the Trillium
 *
 * @author  Kilte Leichnam <nwotnbm@gmail.com>
 * @package Trillium
 */

namespace Trillium\Subscriber;

use Kilte\View\Environment;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * ControllerResponse Class
 *
 * @package Trillium\Subscriber
 */
class ControllerResponse implements EventSubscriberInterface
{

    /**
     * @var array
     */
    private $configuration;

    /**
     * @var Environment
     */
    private $view;

    /**
     * @param array       $configuration
     * @param Environment $view
     */
    public function __construct(array $configuration, Environment $view = null)
    {
        $this->configuration = $configuration;
        $this->view          = $view;
    }

    /**
     * Creates a response
     *
     * @param GetResponseForControllerResultEvent $event
     *
     * @throws \RuntimeException
     * @return void
     */
    public function onControllerResponse(GetResponseForControllerResultEvent $event)
    {
        $result = $event->getControllerResult();
        if ($result instanceof Response) {
            return;
        }
        $request    = $event->getRequest();
        $controller = $request->attributes->get('_controller');
        $action     = $request->attributes->get('_action');
        $type       = null;
        if (isset($this->configuration[$controller])) {
            $actions = $this->configuration[$controller];
            if (isset($actions[$action])) {
                $type = $actions[$action];
            }
        }
        if ($type == 'json') {
            if (isset($result['_status'])) {
                $status = $result['_status'];
                unset($result['_status']);
            } else {
                $status = 200;
            }
            $event->setResponse(new JsonResponse($result, $status));
        } elseif (is_array($type)) {
            $title  = isset($result['_title']) ? $result['_title'] : 'Trillium';
            $status = isset($result['_status']) ? $result['_status'] : 200;
            if (isset($type['view'])) {
                $result = $this->view->load($type['view'], $result);
            }
            if (!isset($type['layout']) || $type['layout'] !== false) {
                $result = $this->view->load(
                    'layout',
                    [
                        'title'   => $title,
                        'content' => $result
                    ]
                );
            }
            $event->setResponse(new Response($result, $status));
        } else {
            throw new \RuntimeException(
                sprintf(
                    'Unable to define response type for controller "%s" with action %s',
                    $controller,
                    $action
                )
            );
        }
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::VIEW => ['onControllerResponse', 128],
        ];
    }

}
