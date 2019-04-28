<?php
/*
 * This file is part of the CleverAge/LayoutBundle package.
 *
 * Copyright (c) 2015-2018 Clever-Age
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CleverAge\LayoutBundle\Templating;

use CleverAge\LayoutBundle\Block\BlockInterface;
use CleverAge\LayoutBundle\Layout\LayoutInterface;
use CleverAge\LayoutBundle\Layout\Slot;
use Symfony\Component\Templating\EngineInterface;

/**
 * {@inheritDoc}
 */
class BlockRenderer implements BlockRendererInterface
{
    /** @var EngineInterface */
    protected $engine;

    /** @var array */
    protected $blockRenders = [];

    /**
     * @param EngineInterface $engine
     */
    public function __construct(EngineInterface $engine)
    {
        $this->engine = $engine;
    }

    /**
     * {@inheritDoc}
     */
    public function renderBlock(LayoutInterface $layout, Slot $slot, BlockInterface $block): string
    {
        $blockDefinition = $slot->getBlockDefinition($block->getCode());
        if (!isset($this->blockRenders[$slot->getCode()][$blockDefinition->getCode()])) {
            try {
                $render = $this->engine->render($block->getTemplate(), $block->getTemplateParameters());
                $this->blockRenders[$slot->getCode()][$blockDefinition->getCode()] = $render;
            } catch (\Throwable $exception) {
                $originalBlockCode = $blockDefinition->getBlockCode();
                throw new \RuntimeException(
                    "Error while rendering block {$originalBlockCode} in layout {$layout->getCode()}",
                    0,
                    $exception
                );
            }
        }

        return $this->blockRenders[$slot->getCode()][$blockDefinition->getCode()];
    }
}
