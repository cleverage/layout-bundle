<?php
/*
 * This file is part of the CleverAge/LayoutBundle package.
 *
 * Copyright (c) 2015-2018 Clever-Age
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CleverAge\LayoutBundle\Block;

use CleverAge\LayoutBundle\Event\BlockInitializationEvent;

/**
 * Represents the base interface for the block services that are used to render a given layout
 * Data shared between blocks should use a common service
 */
interface BlockInterface
{
    /**
     * Set the block data depending on the current request
     *
     * @param BlockInitializationEvent $event
     */
    public function initialize(BlockInitializationEvent $event): void;

    /**
     * Return the template that should be used for rendering
     *
     * @return string
     */
    public function getTemplate(): string;

    /**
     * Return the template parameters
     *
     * @return array
     */
    public function getTemplateParameters(): array;

    /**
     * Skip the render if true
     *
     * @return bool
     */
    public function isDisplayed(): bool;

    /**
     * Get the code of the block
     *
     * @return string
     */
    public function getCode(): string;
}
