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
class DebugSlotRenderer extends AbstractDebugRenderer implements SlotRendererInterface
{
    /** @var SlotRendererInterface */
    protected $baseSlotRenderer;

    /**
     * @param SlotRendererInterface $baseSlotRenderer
     * @param EngineInterface       $engine
     * @param Stopwatch|null        $stopwatch
     * @param bool                  $debugMode
     */
    public function __construct(
        SlotRendererInterface $baseSlotRenderer,
        EngineInterface $engine,
        ?Stopwatch $stopwatch,
        bool $debugMode = false
    ) {
        $this->baseSlotRenderer = $baseSlotRenderer;
        $this->engine = $engine;
        $this->stopwatch = $stopwatch;
        $this->debugMode = $debugMode;
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
