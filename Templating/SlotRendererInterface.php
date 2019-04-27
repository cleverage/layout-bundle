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

/**
 * Render slots inside a specific layout
 */
interface SlotRendererInterface
{
    /**
     * @param LayoutInterface $layout
     * @param string          $slotCode
     *
     * @return string
     */
    public function renderSlot(LayoutInterface $layout, string $slotCode): string;
}
