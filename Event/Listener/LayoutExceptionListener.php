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
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * Handles exceptions in layouts views
 */
class LayoutExceptionListener
{
    /** @var LayoutRenderer */
    protected $layoutRenderer;

    /** @var LoggerInterface */
    protected $logger;

    /** @var KernelInterface */
    protected $kernel;

    /**
     * @param LayoutRenderer  $layoutRenderer
     * @param LoggerInterface $logger
     * @param KernelInterface $kernel
     */
    public function __construct(LayoutRenderer $layoutRenderer, LoggerInterface $logger, KernelInterface $kernel)
    {
        $this->layoutRenderer = $layoutRenderer;
        $this->logger = $logger;
        $this->kernel = $kernel;
    }

    /**
     * @param GetResponseForExceptionEvent $event
     *
     * @throws \InvalidArgumentException
     * @throws \RuntimeException
     */
    public function onKernelException(GetResponseForExceptionEvent $event): void
    {
        $request = $event->getRequest();
        $uri = $request->getRequestUri();
        if ($event->getException() instanceof NotFoundHttpException && '/' === substr($uri, -1)) {
            $event->setResponse(new RedirectResponse(rtrim($uri, '/')));
            $event->stopPropagation();

            return;
        }
        $layoutAnnotation = $request->get('_layout');

        if (!$layoutAnnotation instanceof Layout) {
            $layoutAnnotation = new Layout([]);
        }

        if ($event->getException() instanceof NotFoundHttpException) {
            $layoutAnnotation->setName('exception_404');
            $this->logger->warning(
                "Catched 404 on {$request->getRequestUri()} : {$event->getException()->getMessage()}"
            );
        } else {
            if ($this->kernel->isDebug()) {
                return;
            }
            $layoutAnnotation->setName('exception_500');
            $this->logger->error(
                "Catched error on '{$request->getRequestUri()}' : '{$event->getException()->getMessage()}'",
                $this->getTraces($event->getException())
            );
        }

        $event->setResponse(
            $this->layoutRenderer->getLayoutResponse($request, $layoutAnnotation->getName())
        );
        $event->stopPropagation();
    }

    /**
     * @param \Throwable $e
     * @param array      $context
     * @param int        $level
     *
     * @return array
     */
    protected function getTraces(\Throwable $e = null, array &$context = [], $level = 0): array
    {
        if (null === $e) {
            return $context;
        }
        $context['message_'.$level] = $e->getMessage();
        $context['trace_'.$level] = $e->getTraceAsString();
        if ($e->getPrevious()) {
            $this->getTraces($e->getPrevious(), $context, $level + 1);
        }

        return $context;
    }
}
