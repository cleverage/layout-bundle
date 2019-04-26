<?php
/*
 * This file is part of the CleverAge/LayoutBundle package.
 *
 * Copyright (c) 2015-2018 Clever-Age
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CleverAge\LayoutBundle\Event;

use CleverAge\LayoutBundle\Layout\LayoutInterface;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Event representing a layout initialization
 */
class LayoutInitializationEvent extends Event
{
    /** @var Request */
    protected $request;

    /** @var LayoutInterface */
    protected $layout;

    /** @var array */
    protected $viewParameters;

    /** @var Response|null */
    protected $response;

    /**
     * @param Request         $request
     * @param LayoutInterface $layout
     * @param array           $viewParameters
     */
    public function __construct(Request $request, LayoutInterface $layout, array $viewParameters = [])
    {
        $this->request = $request;
        $this->layout = $layout;

        $this->viewParameters = array_merge(['layout' => $layout], $layout->getGlobalParameters(), $viewParameters);
    }

    /**
     * @return Request
     */
    public function getRequest(): Request
    {
        return $this->request;
    }

    /**
     * @return LayoutInterface
     */
    public function getLayout(): LayoutInterface
    {
        return $this->layout;
    }

    /**
     * @return Response|null
     */
    public function getResponse(): ?Response
    {
        return $this->response;
    }

    /**
     * @param Response $response
     */
    public function setResponse(Response $response): void
    {
        $this->response = $response;
    }

    /**
     * @return array
     */
    public function getViewParameters(): array
    {
        return $this->viewParameters;
    }

    /**
     * @param array $viewParameters
     */
    public function setViewParameters(array $viewParameters): void
    {
        $this->viewParameters = $viewParameters;
    }
}
