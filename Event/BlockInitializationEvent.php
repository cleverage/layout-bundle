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

use CleverAge\LayoutBundle\Layout\BlockDefinition;

/**
 * Event passed to blocks for initialization
 */
class BlockInitializationEvent extends SlotInitializationEvent
{
    /** @var BlockDefinition */
    protected $blockDefinition;

    /** @var array */
    protected $blockParameters;

    /**
     * @param SlotInitializationEvent $parentEvent
     * @param BlockDefinition         $blockDefinition
     */
    public function __construct(SlotInitializationEvent $parentEvent, BlockDefinition $blockDefinition)
    {
        parent::__construct($parentEvent, $parentEvent->getSlot());
        $this->blockDefinition = $blockDefinition;
        $this->blockParameters = array_merge($this->getViewParameters(), $blockDefinition->getParameters());
    }

    /**
     * @return BlockDefinition
     */
    public function getBlockDefinition(): BlockDefinition
    {
        return $this->blockDefinition;
    }

    /**
     * @return array
     */
    public function getBlockParameters(): array
    {
        return $this->blockParameters;
    }

    /**
     * @param array $blockParameters
     */
    public function setBlockParameters(array $blockParameters): void
    {
        $this->blockParameters = $blockParameters;
    }
}
