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
use Symfony\Component\HttpFoundation\Request;

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
     * @param string $slotCode
     *
     * @return array
     */
    public function getBlocksHtml(string $slotCode): array;

    /**
     * @param string $slotCode
     *
     * @throws MissingSlotException
     *
     * @return Slot
     */
    public function getSlot(string $slotCode): Slot;

    /**
     * @param string[] $slotCodes
     *
     * @return int
     */
    public function getSlotBlockCount(array $slotCodes): int;

    /**
     * @return array
     */
    public function getGlobalParameters(): array;

    /**
     * @param Request $request
     */
    public function initializeBlocks(Request $request);
}
