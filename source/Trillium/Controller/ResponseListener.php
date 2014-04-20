<?php

/**
 * Part of the Trillium
 *
 * @author  Kilte Leichnam <nwotnbm@gmail.com>
 * @package Trillium
 */

namespace Trillium\Controller;

use Kilte\View\Environment;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * ResponseListener Class
 *
 * @package Trillium\Controller
 */
class ResponseListener implements EventSubscriberInterface
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
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::VIEW => ['onControllerResponse', 128],
        ];
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
        if ($type == 'json' || $type === null) {
            if (isset($result['_status'])) {
                $status = $result['_status'];
                unset($result['_status']);
            } else {
                $status = 200;
            }
            $event->setResponse(new JsonResponse($result, $status));
        } elseif (is_array($type) && isset($type['view'])) {
            $status  = isset($result['_status']) ? $result['_status'] : 200;
            $headers = isset($result['_headers']) ? $result['_headers'] : [];
            $result  = $this->view->load($type['view'], $result, $headers);
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

}
