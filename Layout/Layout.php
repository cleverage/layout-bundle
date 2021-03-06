<?php

namespace CleverAge\LayoutBundle\Layout;

use CleverAge\LayoutBundle\Block\BlockInterface;
use CleverAge\LayoutBundle\Registry\BlockRegistry;
use CleverAge\LayoutBundle\Exception\MissingSlotException;
use CleverAge\LayoutBundle\Exception\MissingBlockException;

/**
 * Represent a layout, hold the main (inherited) parameters and manage slot rendering
 */
class Layout implements LayoutInterface
{
    /** @var BlockRegistry */
    protected $blockRegistry;

    /** @var string */
    protected $code;

    /** @var string */
    protected $template;

    /** @var array */
    protected $globalParameters;

    /** @var Slot[] */
    protected $slots;

    /** @var  BlockInterface[] */
    protected $blocks;

    /**
     * @param BlockRegistry $blockRegistry
     * @param string        $code
     * @param string        $template
     * @param Slot[]        $slots
     * @param array         $globalParameters
     */
    public function __construct(
        BlockRegistry $blockRegistry,
        string $code,
        string $template,
        array $slots,
        array $globalParameters = []
    ) {
        $this->blockRegistry = $blockRegistry;
        $this->code = $code;
        $this->template = $template;
        $this->slots = $slots;
        $this->globalParameters = $globalParameters;
    }

    /**
     * @return string
     */
    public function getCode(): string
    {
        return $this->code;
    }

    /**
     * @return string
     */
    public function getTemplate(): string
    {
        return $this->template;
    }

    /**
     * @return array
     */
    public function getGlobalParameters(): array
    {
        return $this->globalParameters;
    }

    /**
     * @return Slot[]
     */
    public function getSlots(): array
    {
        return $this->slots;
    }

    /**
     * @param string $slotCode
     *
     * @throws MissingSlotException
     *
     * @return Slot
     */
    public function getSlot(string $slotCode): Slot
    {
        if (!array_key_exists($slotCode, $this->slots)) {
            throw MissingSlotException::create($slotCode);
        }

        return $this->slots[$slotCode];
    }

    /**
     * @param string $code
     *
     * @throws MissingBlockException
     *
     * @return BlockInterface
     */
    public function getBlock(string $code): BlockInterface
    {
        return $this->blockRegistry->getBlock($code);
    }
}
