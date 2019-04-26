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

use CleverAge\LayoutBundle\Layout\LayoutInterface;
use CleverAge\LayoutBundle\Templating\SlotRendererInterface;
use Symfony\Component\Stopwatch\Stopwatch;

/**
 * Decorates the base slot renderer to time the rendering of slots
 */
class DebugSlotRenderer implements SlotRendererInterface
{
    /** @var SlotRendererInterface */
    protected $baseSlotRenderer;

    /** @var Stopwatch|null */
    protected $stopwatch;

    /**
     * @param SlotRendererInterface $baseSlotRenderer
     * @param Stopwatch|null        $stopwatch
     */
    public function __construct(SlotRendererInterface $baseSlotRenderer, ?Stopwatch $stopwatch)
    {
        $this->baseSlotRenderer = $baseSlotRenderer;
        $this->stopwatch = $stopwatch;
    }

    /**
     * {@inheritDoc}
     */
    public function renderSlot(LayoutInterface $layout, string $slotCode): \Generator
    {
        if ($this->stopwatch) {
            $this->stopwatch->start("render.slot.{$slotCode}");
        }

        yield from $this->baseSlotRenderer->renderSlot($layout, $slotCode);

        if ($this->stopwatch) {
            $this->stopwatch->stop("render.slot.{$slotCode}");
        }
    }

    /**
     * {@inheritdoc}
     */
    public function isEmptySlot(LayoutInterface $layout, string $slotCode): bool
    {
        if ($this->stopwatch) {
            $this->stopwatch->start("render.slot.{$slotCode}");
        }

        $result = $this->baseSlotRenderer->isEmptySlot($layout, $slotCode);

        if ($this->stopwatch) {
            $this->stopwatch->stop("render.slot.{$slotCode}");
        }

        return $result;
    }
}
