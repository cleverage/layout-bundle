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
 * Renders blocks
 */
interface BlockRendererInterface
{
    /**
     * @param LayoutInterface $layout
     * @param Slot            $slot
     * @param BlockInterface  $block
     *
     * @return string
     */
    public function renderBlock(LayoutInterface $layout, Slot $slot, BlockInterface $block): string;
}
