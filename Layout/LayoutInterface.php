<?php
/*
 * This file is part of the CleverAge/LayoutBundle package.
 *
 * Copyright (c) 2015-2018 Clever-Age
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CleverAge\LayoutBundle\Layout;

use CleverAge\LayoutBundle\Exception\MissingSlotException;

/**
 * Defines how layouts are rendered and holds the configuration of a defined layout
 */
interface LayoutInterface
{
    /**
     * @return string
     */
    public function getCode(): string;

    /**
     * @return string
     */
    public function getTemplate(): string;

    /**
     * @return Slot[]
     */
    public function getSlots(): array;

    /**
     * @param string $slotCode
     *
     * @throws MissingSlotException
     *
     * @return Slot
     */
    public function getSlot(string $slotCode): Slot;

    /**
     * @return array
     */
    public function getGlobalParameters(): array;
}
