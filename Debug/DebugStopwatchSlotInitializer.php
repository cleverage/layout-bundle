<?php
/*
 * This file is part of the CleverAge/LayoutBundle package.
 *
 * Copyright (c) 2015-2018 Clever-Age
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CleverAge\LayoutBundle\Debug;

use CleverAge\LayoutBundle\Event\SlotInitializationEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Stopwatch\Stopwatch;

/**
 * Decorates the base slot initializer to time the initialization of slots
 */
class DebugStopwatchSlotInitializer implements EventSubscriberInterface
{
    /** @var Stopwatch|null */
    protected $stopwatch;

    /**
     * @param Stopwatch|null $stopwatch
     */
    public function __construct(?Stopwatch $stopwatch)
    {
        $this->stopwatch = $stopwatch;
    }

    /**
     * {@inheritDoc}
     */
    public static function getSubscribedEvents(): array
    {
        return [
            'slot.initialize' => [
                ['onBeginInitialize', 255],
                ['onEndInitialize', -255],
            ],
        ];
    }

    /**
     * @param SlotInitializationEvent $event
     */
    public function onBeginInitialize(SlotInitializationEvent $event): void
    {
        if (!$this->stopwatch) {
            return;
        }
        $this->stopwatch->start("slot.initialize.{$event->getSlot()->getCode()}");
    }

    /**
     * @param SlotInitializationEvent $event
     */
    public function onEndInitialize(SlotInitializationEvent $event): void
    {
        if (!$this->stopwatch) {
            return;
        }
        $this->stopwatch->stop("slot.initialize.{$event->getSlot()->getCode()}");
    }
}
