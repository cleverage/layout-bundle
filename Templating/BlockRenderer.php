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

/**
 * {@inheritDoc}
 */
class BlockRenderer implements BlockRendererInterface
{
    /** @var array */
    protected $blockRenders = [];

    /**
     * {@inheritDoc}
     */
    public function renderBlock(LayoutInterface $layout, Slot $slot, BlockInterface $block): string
    {
        $blockDefinition = $slot->getBlockDefinition($block->getCode());
        if (!isset($this->blockRenders[$slot->getCode()][$blockDefinition->getCode()])) {
            $parameters = array_merge(
                [
                    '_layout' => $this,
                    '_slot' => $slot,
                    '_block_definition' => $blockDefinition,
                    '_block' => $block,
                ],
                $layout->getGlobalParameters(),
                $blockDefinition->getParameters()
            );
            try {
                $this->blockRenders[$slot->getCode()][$blockDefinition->getCode()] = $block->render($parameters);
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
