<?php

namespace CleverAge\LayoutBundle\Event\Listener;

use CleverAge\LayoutBundle\Event\LayoutInitializationEvent;
use CleverAge\LayoutBundle\Event\SlotInitializationEvent;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Initialize layout
 */
class LayoutInitializer
{
    /** @var EventDispatcherInterface */
    protected $eventDispatcher;

    /** @var EngineInterface */
    protected $templating;

    /**
     * @param EventDispatcherInterface $eventDispatcher
     * @param EngineInterface          $templating
     */
    public function __construct(EventDispatcherInterface $eventDispatcher, EngineInterface $templating)
    {
        $this->eventDispatcher = $eventDispatcher;
        $this->templating = $templating;
    }

    /**
     * @param LayoutInitializationEvent $layoutEvent
     */
    public function onLayoutInitialize(LayoutInitializationEvent $layoutEvent): void
    {
        foreach ($layoutEvent->getLayout()->getSlots() as $slot) {
            $slotEvent = new SlotInitializationEvent($layoutEvent, $slot);
            $this->eventDispatcher->dispatch('slot.initialize', $slotEvent);
            if ($slotEvent->getResponse()) {
                $layoutEvent->setResponse($slotEvent->getResponse());

                return;
            }
        }

        $response = $this->templating->renderResponse(
            $layoutEvent->getLayout()->getTemplate(),
            $layoutEvent->getViewParameters()
        );

        $layoutEvent->setResponse($response);
    }
}
