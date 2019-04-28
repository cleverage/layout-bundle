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

use CleverAge\LayoutBundle\Layout\LayoutInterface;
use CleverAge\LayoutBundle\Registry\BlockRegistry;

/**
 * {@inheritDoc}
 */
class SlotRenderer implements SlotRendererInterface
{
    /** @var BlockRegistry */
    protected $blockRegistry;

    /** @var BlockRendererInterface */
    protected $blockRenderer;

    /**
     * @param BlockRegistry          $blockRegistry
     * @param BlockRendererInterface $blockRenderer
     */
    public function __construct(BlockRegistry $blockRegistry, BlockRendererInterface $blockRenderer)
    {
        $this->blockRegistry = $blockRegistry;
        $this->blockRenderer = $blockRenderer;
    }

    /**
     * {@inheritDoc}
     */
    public function renderSlot(LayoutInterface $layout, string $slotCode): string
    {
        $html = '';
        $slot = $layout->getSlot($slotCode);
        foreach ($slot->getBlockDefinitions() as $blockDefinition) {
            if (!$blockDefinition->isDisplayed()) {
                continue;
            }
            $block = $this->blockRegistry->getBlock($blockDefinition->getBlockCode(), $slot);
            $html .= $this->blockRenderer->renderBlock($layout, $slot, $block);
        }

        return $html;
    }
}
