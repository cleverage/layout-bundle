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

use CleverAge\LayoutBundle\Layout\Slot;

/**
 * Event representing a slot initialization
 */
class SlotInitializationEvent extends LayoutInitializationEvent
{
    /** @var Slot */
    protected $slot;

    /**
     * @param LayoutInitializationEvent $parentEvent
     * @param Slot                      $slot
     */
    public function __construct(LayoutInitializationEvent $parentEvent, Slot $slot)
    {
        parent::__construct($parentEvent->getRequest(), $parentEvent->getLayout(), $parentEvent->getViewParameters());
        $this->slot = $slot;
    }

    /**
     * @return Slot
     */
    public function getSlot(): Slot
    {
        return $this->slot;
    }
}
