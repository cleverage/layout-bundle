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
        if (null === $layoutAnnotation || '' === $layoutAnnotation) {
            return; // No layout
        }

        if (\is_string($layoutAnnotation)) {
            $layoutAnnotation = new Layout(['name' => $layoutAnnotation]);
            $request->attributes->set('_layout', $layoutAnnotation);
        }
        if (!$layoutAnnotation instanceof Layout || !$layoutAnnotation->getName()) {
            throw new \UnexpectedValueException("Missing layout name for route {$request->attributes->get('_route')}");
        }
    }
}
