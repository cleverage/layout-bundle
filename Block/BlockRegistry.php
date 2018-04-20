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

use CleverAge\LayoutBundle\Exception\DuplicatedBlockException;
use CleverAge\LayoutBundle\Exception\MissingBlockException;
use Symfony\Component\Cache\Adapter\AdapterInterface;

/**
 * Holds all the block services, automatically injected through the clever.block tag
 */
class BlockRegistry
{
    /** @var \Twig_Environment */
    protected $twig;

    /** @var AdapterInterface */
    protected $cacheAdapter;

    /**
     * Application wide switch
     *
     * @var bool
     */
    protected $enableCache;


    /** @var BlockInterface[] */
    protected $blocks = [];

    /**
     * BlockRegistry constructor.
     *
     * @param \Twig_Environment $twig
     * @param AdapterInterface  $cacheAdapter
     * @param bool              $enableCache
     */
    public function __construct($twig = null, $cacheAdapter = null, bool $enableCache = true)
    {
        $this->twig = $twig;
        $this->cacheAdapter = $cacheAdapter;
        $this->enableCache = $enableCache;
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
    public function addBlock(BlockInterface $block)
    {
        if ($this->hasBlock($block->getCode())) {
            throw DuplicatedBlockException::create($block->getCode());
        }

        // Automatically inject services, if available
        if (isset($this->twig) && $block instanceof TwigAwareBlockInterface) {
            $block->setTwig($this->twig);
        }
        if (isset($this->cacheAdapter) && $block instanceof CacheAdapterAwareBlockInterface) {
            $block->setEnableCache($this->enableCache);
            $block->setCacheAdapter($this->cacheAdapter);
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
