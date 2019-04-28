<?php
/*
 * This file is part of the CleverAge/LayoutBundle package.
 *
 * Copyright (c) 2015-2018 Clever-Age
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CleverAge\LayoutBundle\Event;

use CleverAge\LayoutBundle\Layout\BlockDefinition;

/**
 * Event passed to blocks for initialization
 */
class BlockInitializationEvent extends SlotInitializationEvent
{
    /** @var BlockDefinition */
    protected $blockDefinition;

    /** @var array */
    protected $templateParameters = [];

    /**
     * @param SlotInitializationEvent $parentEvent
     * @param BlockDefinition         $blockDefinition
     */
    public function __construct(SlotInitializationEvent $parentEvent, BlockDefinition $blockDefinition)
    {
        parent::__construct($parentEvent, $parentEvent->getSlot());
        $this->blockDefinition = $blockDefinition;
    }

    /**
     * @return BlockDefinition
     */
    public function getBlockDefinition(): BlockDefinition
    {
        return $this->blockDefinition;
    }

    /**
     * @return array
     */
    public function getTemplateParameters(): array
    {
        return $this->templateParameters;
    }

    /**
     * @param array $templateParameters
     */
    public function setTemplateParameters(array $templateParameters): void
    {
        $this->templateParameters = $templateParameters;
    }

    /**
     * @param string $key
     * @param mixed  $value
     */
    public function addTemplateParameter(string $key, $value): void
    {
        $this->templateParameters[$key] = $value;
    }
}
