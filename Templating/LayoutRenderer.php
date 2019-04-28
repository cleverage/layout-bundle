<?php
/*
 * This file is part of the CleverAge/LayoutBundle package.
 *
 * Copyright (c) 2015-2018 Clever-Age
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CleverAge\LayoutBundle\Templating;

use CleverAge\LayoutBundle\Event\LayoutInitializationEvent;
use CleverAge\LayoutBundle\Registry\LayoutRegistry;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Handles layout rendering based on request and optinal additional parameters
 */
class LayoutRenderer
{
    /** @var LayoutRegistry */
    protected $layoutRegistry;

    /** @var EngineInterface */
    protected $templating;

    /** @var EventDispatcherInterface */
    protected $eventDispatcher;

    /**
     * @param LayoutRegistry           $layoutRegistry
     * @param EngineInterface          $templating
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function __construct(
        LayoutRegistry $layoutRegistry,
        EngineInterface $templating,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->layoutRegistry = $layoutRegistry;
        $this->templating = $templating;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @param Request $request
     * @param string  $layoutCode
     * @param array   $controllerResponse
     *
     * @return Response
     */
    public function getLayoutResponse(Request $request, $layoutCode, array $controllerResponse = []): Response
    {
        $layout = $this->layoutRegistry->getLayout($layoutCode);

        // Initialization
        $event = new LayoutInitializationEvent($request, $layout, $controllerResponse);
        $this->eventDispatcher->dispatch('layout.initialize', $event);

        // Rendering
        return $event->getResponse();
    }
}
