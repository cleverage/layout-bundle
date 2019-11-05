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
use Symfony\Component\Templating\EngineInterface;

/**
 * Decorates the base slot renderer to time the rendering of slots
 */
class DebugStopwatchSlotRenderer extends AbstractDebugStopwatchRenderer implements SlotRendererInterface
{
    /** @var SlotRendererInterface */
    protected $baseSlotRenderer;

    /**
     * @param SlotRendererInterface $baseSlotRenderer
     * @param Stopwatch|null        $stopwatch
     */
    public function __construct(
        SlotRendererInterface $baseSlotRenderer,
        ?Stopwatch $stopwatch
    ) {
        $this->baseSlotRenderer = $baseSlotRenderer;
        $this->stopwatch = $stopwatch;
    }

    /**
     * {@inheritDoc}
     */
    public function renderSlot(LayoutInterface $layout, string $slotCode): string
    {
        return $this->wrapHtml(
            'slot',
            $slotCode,
            function () use ($layout, $slotCode) {
                return $this->baseSlotRenderer->renderSlot($layout, $slotCode);
            }
        );
    }
}
