<?php

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
     * @param string[] ...$slotCodes
     *
     * @return int
     */
    public function getSlotBlockCount(string ...$slotCodes);

    /**
     * @return array
     */
    public function getGlobalParameters(): array;

    /**
     * @param Request $request
     */
    public function initializeBlocks(Request $request);
}
