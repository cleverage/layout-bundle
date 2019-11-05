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

use CleverAge\LayoutBundle\Event\BlockInitializationEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Stopwatch\Stopwatch;

/**
 * Decorates the base block initializer to time the initialization of blocks
 */
class DebugStopwatchBlockInitializer implements EventSubscriberInterface
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
            'block.initialize' => [
                ['onBeginInitialize', 255],
                ['onEndInitialize', -255],
            ],
        ];
    }

    /**
     * @param BlockInitializationEvent $event
     */
    public function onBeginInitialize(BlockInitializationEvent $event): void
    {
        if (!$this->stopwatch) {
            return;
        }
        $this->stopwatch->start("block.initialize.{$event->getBlockDefinition()->getCode()}");
    }

    /**
     * @param BlockInitializationEvent $event
     */
    public function onEndInitialize(BlockInitializationEvent $event): void
    {
        if (!$this->stopwatch) {
            return;
        }
        $this->stopwatch->stop("block.initialize.{$event->getBlockDefinition()->getCode()}");
    }
}
