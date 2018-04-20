<?php

namespace CleverAge\LayoutBundle\EventListener;


use CleverAge\LayoutBundle\Annotation\Layout;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * Resolve block layout for a given controller response
 */
class LayoutListener implements EventSubscriberInterface
{
    /** @var ContainerInterface */
    protected $container;

    /**
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * Set the Layout name if undefined
     *
     * @param FilterControllerEvent $event
     */
    public function onKernelController(FilterControllerEvent $event)
    {
        $request = $event->getRequest();
        $layoutAnnotation = $request->attributes->get('_layout');

        if ($layoutAnnotation instanceof Layout && !$layoutAnnotation->getName()) {
            $controllerCallable = $event->getController();
            $controllerClass = get_class($controllerCallable[0]);
            $controllerClassParts = explode('\\', $controllerClass);
            $controllerName = end($controllerClassParts);
            $controllerName = strtolower(str_replace('Controller', '', $controllerName));
            $actionName = $controllerCallable[1];
            $actionName = strtolower(str_replace('Action', '', $actionName));

            $layoutAnnotation->setName($controllerName.'_'.$actionName);
        } elseif (is_string($layoutAnnotation) && !empty($layoutAnnotation)) {
            $request->attributes->set('_layout', new Layout(['name' => $layoutAnnotation]));
        }
    }

    /**
     * Set the Response using the layout render
     *
     * @param GetResponseForControllerResultEvent $event
     *
     * @throws \Exception
     */
    public function onKernelView(GetResponseForControllerResultEvent $event)
    {
        $request = $event->getRequest();
        $layoutAnnotation = $request->attributes->get('_layout');

        if (!$layoutAnnotation instanceof Layout) {
            return;
        }

        $layout = $this->container->get('clever_age_layout.registry.layout')->getLayout($layoutAnnotation->getName());

        // Prepare view parameters
        $specificParameters = $event->getControllerResult();
        if ($specificParameters instanceof Response) {
            return;
        }

        $parameters = array_merge(['layout' => $layout], $layout->getGlobalParameters(), $specificParameters);

        // Initialization
        $layout->initializeBlocks($request);

        // Rendering
        $templating = $this->container->get('templating');
        $event->setResponse($templating->renderResponse($layout->getTemplate(), $parameters));
    }

    /**
     * @param GetResponseForExceptionEvent $event
     */
    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        $request = $event->getRequest();
        $layoutAnnotation = $request->get('_layout');

        // Only in the layout context
        // TODO better handling of those cases
        if ($layoutAnnotation instanceof Layout) {
            if ($event->getException() instanceof NotFoundHttpException) {
                $layoutAnnotation->setName('exception_404');
                $this->container->get('logger')->warn(
                    "Catched 404 on {$request->getRequestUri()} : {$event->getException()->getMessage()}"
                );
            } else {
                if ($this->container->get('kernel')->isDebug()) {
                    return;
                }
                $layoutAnnotation->setName('exception_500');
                $this->container->get('logger')->error(
                    "Catched error on {$request->getRequestUri()} : {$event->getException()->getMessage()}",
                    ['trace' => $event->getException()->getTraceAsString()]
                );
            }

            $layout = $this->container->get('clever_age_layout.registry.layout')->getLayout(
                $layoutAnnotation->getName()
            );
            $parameters = array_merge(['layout' => $layout], $layout->getGlobalParameters());

            // Initialization
            $layout->initializeBlocks($request);

            // Rendering
            $templating = $this->container->get('templating');
            $event->setResponse($templating->renderResponse($layout->getTemplate(), $parameters));
            $event->stopPropagation();
        }
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::CONTROLLER => ['onKernelController', -128],
            KernelEvents::VIEW => 'onKernelView',
            KernelEvents::EXCEPTION => 'onKernelException',
        ];
    }
}
