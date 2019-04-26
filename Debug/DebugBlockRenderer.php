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

/**
 * Decorates the base block renderer to time the rendering of blocks
 */
class DebugBlockRenderer implements BlockRendererInterface
{
    /** @var BlockRendererInterface */
    protected $baseBlockRenderer;

    /** @var Stopwatch|null */
    protected $stopwatch;

    /**
     * @param BlockRendererInterface $baseBlockRenderer
     * @param Stopwatch|null         $stopwatch
     */
    public function __construct(BlockRendererInterface $baseBlockRenderer, ?Stopwatch $stopwatch)
    {
        $this->baseBlockRenderer = $baseBlockRenderer;
        $this->stopwatch = $stopwatch;
    }

    /**
     * {@inheritDoc}
     */
    public function renderBlock(LayoutInterface $layout, Slot $slot, BlockInterface $block): string
    {
        if ($this->stopwatch) {
            $this->stopwatch->start("render.block.{$block->getCode()}");
        }

        $result = $this->baseBlockRenderer->renderBlock($layout, $slot, $block);

        if ($this->stopwatch) {
            $this->stopwatch->stop("render.block.{$block->getCode()}");
        }

        return $result;
    }
}
