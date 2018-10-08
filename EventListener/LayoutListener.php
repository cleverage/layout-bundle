<?php
/*
 * This file is part of the CleverAge/LayoutBundle package.
 *
 * Copyright (c) 2015-2018 Clever-Age
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CleverAge\LayoutBundle\EventListener;

use CleverAge\LayoutBundle\Annotation\Layout;
use CleverAge\LayoutBundle\Layout\LayoutRegistry;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * Resolve block layout for a given controller response
 */
class LayoutListener implements EventSubscriberInterface
{
    /** @var LayoutRegistry */
    protected $layoutRegistry;

    /** @var EngineInterface */
    protected $templating;

    /** @var LoggerInterface */
    protected $logger;

    /** @var KernelInterface */
    protected $kernel;

    /**
     * @param LayoutRegistry  $layoutRegistry
     * @param EngineInterface $templating
     * @param LoggerInterface $logger
     * @param KernelInterface $kernel
     */
    public function __construct(
        LayoutRegistry $layoutRegistry,
        EngineInterface $templating,
        LoggerInterface $logger,
        KernelInterface $kernel
    ) {
        $this->layoutRegistry = $layoutRegistry;
        $this->templating = $templating;
        $this->logger = $logger;
        $this->kernel = $kernel;
    }

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
            $controllerCallable = $event->getController();
            $controllerClass = \get_class($controllerCallable[0]);
            $controllerClassParts = explode('\\', $controllerClass);
            $controllerName = end($controllerClassParts);
            $controllerName = strtolower(str_replace('Controller', '', $controllerName));
            $actionName = $controllerCallable[1];
            $actionName = strtolower(str_replace('Action', '', $actionName));

            $layoutAnnotation->setName($controllerName.'_'.$actionName);
        } elseif (\is_string($layoutAnnotation) && !empty($layoutAnnotation)) {
            $request->attributes->set('_layout', new Layout(['name' => $layoutAnnotation]));
        }
    }

    /**
     * Set the Response using the layout render
     *
     * @param GetResponseForControllerResultEvent $event
     *
     *
     */
    public function onKernelView(GetResponseForControllerResultEvent $event): void
    {
        $request = $event->getRequest();
        $layoutAnnotation = $request->attributes->get('_layout');

        if (!$layoutAnnotation instanceof Layout) {
            return;
        }

        $layout = $this->layoutRegistry->getLayout($layoutAnnotation->getName());

        // Prepare view parameters
        $specificParameters = $event->getControllerResult();
        if ($specificParameters instanceof Response) {
            return;
        }

        $parameters = array_merge(['layout' => $layout], $layout->getGlobalParameters(), $specificParameters);

        // Initialization
        $layout->initializeBlocks($request);

        // Rendering
        $event->setResponse($this->templating->renderResponse($layout->getTemplate(), $parameters));
    }

    /**
     * @param GetResponseForExceptionEvent $event
     *
     * @throws \InvalidArgumentException
     * @throws \RuntimeException
     * @throws \Symfony\Component\DependencyInjection\Exception\ServiceCircularReferenceException
     * @throws \Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException
     * @throws \CleverAge\LayoutBundle\Exception\MissingLayoutException
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

        $layout = $this->layoutRegistry->getLayout(
            $layoutAnnotation->getName()
        );
        $parameters = array_merge(['layout' => $layout], $layout->getGlobalParameters());

        // Initialization
        $layout->initializeBlocks($request);

        // Rendering
        $event->setResponse($this->templating->renderResponse($layout->getTemplate(), $parameters));
        $event->stopPropagation();
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::CONTROLLER => ['onKernelController', -128],
            KernelEvents::VIEW => 'onKernelView',
            KernelEvents::EXCEPTION => 'onKernelException',
        ];
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
