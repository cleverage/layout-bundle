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
use CleverAge\LayoutBundle\Block\CacheAdapterAwareBlockInterface;
use CleverAge\LayoutBundle\Block\RendererAwareBlockInterface;
use CleverAge\LayoutBundle\Exception\DuplicatedBlockException;
use CleverAge\LayoutBundle\Exception\MissingBlockException;
use Cocur\Slugify\Slugify;
use Symfony\Component\Cache\Adapter\AdapterInterface;
use Symfony\Component\Templating\EngineInterface;

/**
 * Holds all the block services, automatically injected through the clever.block tag
 */
class BlockRegistry
{
    /** @var EngineInterface */
    protected $renderer;

    /** @var BlockInterface[] */
    protected $blocks = [];

    /** @var AdapterInterface */
    protected $cacheAdapter;

    /** @var Slugify */
    protected $slugifier;

    /**
     * Application wide switch
     *
     * @var bool
     */
    protected $enableCache;

    /**
     * @param EngineInterface  $renderer
     * @param AdapterInterface $cacheAdapter
     * @param bool             $enableCache
     */
    public function __construct(EngineInterface $renderer, $cacheAdapter = null, bool $enableCache = true)
    {
        $this->renderer = $renderer;
        $this->cacheAdapter = $cacheAdapter;
        $this->enableCache = $enableCache;
        $this->slugifier = Slugify::create(
            [
                'lowercase' => false,
                'regexp' => '/([^A-Za-z0-9%_=]|-)+/',
            ]
        );
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

        // Automatically inject services, if available
        if ($block instanceof RendererAwareBlockInterface) {
            $block->setRenderer($this->renderer);
        }
        if (null !== $this->cacheAdapter && $block instanceof CacheAdapterAwareBlockInterface) {
            $block->setEnableCache($this->enableCache);
            $block->setCacheAdapter($this->cacheAdapter);
            $block->setSlugifier($this->slugifier);
        }

        // Register the block
        $this->blocks[$block->getCode()] = $block;
    }

    /**
     * @param string $blockCode
     *
     * @throws MissingBlockException
     *
     * @return BlockInterface
     */
    public function getBlock(string $blockCode): BlockInterface
    {
        if (!$this->hasBlock($blockCode)) {
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
