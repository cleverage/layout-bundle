<?php

namespace CleverAge\LayoutBundle\Event\Listener;

use CleverAge\LayoutBundle\Event\BlockInitializationEvent;
use CleverAge\LayoutBundle\Event\SlotInitializationEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Initialize slots inside layout
 */
class SlotInitializer
{
    /** @var EventDispatcherInterface */
    protected $eventDispatcher;

    /**
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function __construct(EventDispatcherInterface $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @param SlotInitializationEvent $slotEvent
     */
    public function onSlotInitialize(SlotInitializationEvent $slotEvent): void
    {
        foreach ($slotEvent->getSlot()->getBlockDefinitions() as $blockDefinition) {
            if (!$blockDefinition->isDisplayed()) {
                continue;
            }
            $blockEvent = new BlockInitializationEvent($slotEvent, $blockDefinition);
            $this->eventDispatcher->dispatch('block.initialize', $blockEvent);
            if ($blockEvent->getResponse()) {
                $slotEvent->setResponse($blockEvent->getResponse());

                return;
            }
        }
    }
}
