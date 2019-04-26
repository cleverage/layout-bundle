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
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;

/**
 * Resolve block layout for each request
 */
class LayoutResolverListener
{
    /**
     * Set the Layout name if undefined
     *
     * @param FilterControllerEvent $event
     */
    public function onKernelController(FilterControllerEvent $event): void
    {
        $request = $event->getRequest();
        $layoutAnnotation = $request->attributes->get('_layout');

        if ($layoutAnnotation instanceof Layout && !$layoutAnnotation->getName()) {
            $this->resolveLayoutTemplate($event, $layoutAnnotation);
        } elseif (\is_string($layoutAnnotation) && !empty($layoutAnnotation)) {
            $request->attributes->set('_layout', new Layout(['name' => $layoutAnnotation]));
        }
    }

    /**
     * @param FilterControllerEvent $event
     * @param Layout                $layoutAnnotation
     */
    protected function resolveLayoutTemplate(FilterControllerEvent $event, Layout $layoutAnnotation): void
    {
        @trigger_error(
            'Magic layout template resolution is deprecated. Always set the layout name in the layout annotation',
            E_USER_DEPRECATED
        );

        $controllerCallable = $event->getController();
        $controllerClass = \get_class($controllerCallable[0]);
        $controllerClassParts = explode('\\', $controllerClass);
        $controllerName = end($controllerClassParts);
        $controllerName = strtolower(str_replace('Controller', '', $controllerName));
        $actionName = $controllerCallable[1];
        $actionName = strtolower(str_replace('Action', '', $actionName));

        $layoutAnnotation->setName($controllerName.'_'.$actionName);
    }
}
