<?php
/*
 * This file is part of the CleverAge/LayoutBundle package.
 *
 * Copyright (c) 2015-2018 Clever-Age
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CleverAge\LayoutBundle\Event\Listener;

use CleverAge\LayoutBundle\Annotation\Layout;
use CleverAge\LayoutBundle\Templating\LayoutRenderer;
use function is_array;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;

/**
 * Resolve block layout for a given controller response
 */
class LayoutViewListener
{
    /** @var LayoutRenderer */
    protected $layoutRenderer;

    /**
     * @param LayoutRenderer $layoutRenderer
     */
    public function __construct(LayoutRenderer $layoutRenderer)
    {
        $this->layoutRenderer = $layoutRenderer;
    }

    /**
     * Set the Response using the layout render
     *
     * @param GetResponseForControllerResultEvent $event
     */
    public function onKernelView(GetResponseForControllerResultEvent $event): void
    {
        $request = $event->getRequest();
        $layoutAnnotation = $request->attributes->get('_layout');

        if (!$layoutAnnotation instanceof Layout) {
            return;
        }

        // Prepare view parameters
        $controllerResponse = $event->getControllerResult();
        if ($controllerResponse instanceof Response) {
            return;
        }
        if (null === $controllerResponse) {
            $controllerResponse = [];
        }
        if (!is_array($controllerResponse)) {
            throw new \UnexpectedValueException('Controller response for layout must be an array');
        }

        // Rendering
        $event->setResponse(
            $this->layoutRenderer->getLayoutResponse($request, $layoutAnnotation->getName(), $controllerResponse)
        );
    }
}
