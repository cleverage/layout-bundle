<?php

namespace CleverAge\LayoutBundle\Event\Listener;

use CleverAge\LayoutBundle\Event\BlockInitializationEvent;
use CleverAge\LayoutBundle\Registry\BlockRegistry;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Initialize a single block
 */
class BlockInitializer
{
    /** @var EventDispatcherInterface */
    protected $eventDispatcher;

    /** @var BlockRegistry */
    protected $blockRegistry;

    /**
     * @param EventDispatcherInterface $eventDispatcher
     * @param BlockRegistry            $blockRegistry
     */
    public function __construct(EventDispatcherInterface $eventDispatcher, BlockRegistry $blockRegistry)
    {
        $this->eventDispatcher = $eventDispatcher;
        $this->blockRegistry = $blockRegistry;
    }

    /**
     * @param BlockInitializationEvent $blockEvent
     */
    public function onBlockInitialize(BlockInitializationEvent $blockEvent): void
    {
        $blockDefinition = $blockEvent->getBlockDefinition();
        $block = $this->blockRegistry->getBlock($blockDefinition->getBlockCode(), $blockEvent->getSlot());
        try {
            $block->initialize($blockEvent);
        } catch (\Throwable $exception) {
            $originalBlockCode = $blockDefinition->getBlockCode();
            throw new \RuntimeException(
                "Error while initializing block {$originalBlockCode} in layout {$blockEvent->getLayout()->getCode()}",
                0,
                $exception
            );
        }
    }
}
