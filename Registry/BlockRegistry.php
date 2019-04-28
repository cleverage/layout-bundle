<?php
/*
 * This file is part of the CleverAge/LayoutBundle package.
 *
 * Copyright (c) 2015-2018 Clever-Age
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CleverAge\LayoutBundle\Registry;

use CleverAge\LayoutBundle\Block\BlockInterface;
use CleverAge\LayoutBundle\Block\SimpleBlock;
use CleverAge\LayoutBundle\Exception\DuplicatedBlockException;
use CleverAge\LayoutBundle\Exception\MissingBlockException;
use CleverAge\LayoutBundle\Layout\Slot;
use Symfony\Component\Templating\EngineInterface;

/**
 * Holds all the block services, automatically injected through the clever.block tag
 */
class BlockRegistry
{
    /** @var EngineInterface */
    protected $engine;

    /** @var BlockInterface[] */
    protected $blocks = [];

    /**
     * @param EngineInterface $engine
     */
    public function __construct(EngineInterface $engine)
    {
        $this->engine = $engine;
    }

    /**
     * @return BlockInterface[]
     */
    public function getBlocks(): array
    {
        return $this->blocks;
    }

    /**
     * @param BlockInterface $block
     *
     * @throws DuplicatedBlockException
     */
    public function addBlock(BlockInterface $block): void
    {
        if ($this->hasBlock($block->getCode())) {
            throw DuplicatedBlockException::create($block->getCode());
        }

        // Register the block
        $this->blocks[$block->getCode()] = $block;
    }

    /**
     * @param string    $blockCode
     * @param Slot|null $slot
     *
     * @return BlockInterface
     */
    public function getBlock(string $blockCode, Slot $slot = null): BlockInterface
    {
        if (!$this->hasBlock($blockCode)) {
            if ($slot) {
                $template = "Layout/{$slot->getCode()}/{$blockCode}.html.twig";
                if ($this->engine->exists($template)) {
                    $block = new SimpleBlock($blockCode, $template);
                    $this->addBlock($block);

                    return $block;
                }
            }
            throw MissingBlockException::create($blockCode);
        }

        return $this->blocks[$blockCode];
    }

    /**
     * @param string $blockCode
     *
     * @return bool
     */
    public function hasBlock(string $blockCode): bool
    {
        return array_key_exists($blockCode, $this->blocks);
    }
}
