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

use CleverAge\LayoutBundle\Block\BlockInterface;
use CleverAge\LayoutBundle\Layout\LayoutInterface;
use CleverAge\LayoutBundle\Layout\Slot;
use CleverAge\LayoutBundle\Templating\BlockRendererInterface;
use Symfony\Component\Stopwatch\Stopwatch;
use Symfony\Component\Templating\EngineInterface;

/**
 * Decorates the base block renderer to time the rendering of blocks
 */
class DebugBlockRenderer extends AbstractDebugRenderer implements BlockRendererInterface
{
    /** @var BlockRendererInterface */
    protected $baseBlockRenderer;

    /**
     * @param BlockRendererInterface $baseBlockRenderer
     * @param EngineInterface        $engine
     * @param Stopwatch|null         $stopwatch
     * @param bool                   $debugMode
     */
    public function __construct(
        BlockRendererInterface $baseBlockRenderer,
        EngineInterface $engine,
        ?Stopwatch $stopwatch,
        bool $debugMode = false
    ) {
        $this->baseBlockRenderer = $baseBlockRenderer;
        $this->engine = $engine;
        $this->stopwatch = $stopwatch;
        $this->debugMode = $debugMode;
    }

    /**
     * {@inheritDoc}
     */
    public function renderBlock(LayoutInterface $layout, Slot $slot, BlockInterface $block): string
    {
        return $this->wrapHtml(
            'block',
            $block->getCode(),
            function () use ($layout, $slot, $block) {
                return $this->baseBlockRenderer->renderBlock($layout, $slot, $block);
            }
        );
    }
}
