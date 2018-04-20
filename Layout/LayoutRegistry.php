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

use CleverAge\LayoutBundle\Exception\MissingLayoutException;

/**
 * Holds all the layout services, automatically injected through the clever.layout tag
 */
class LayoutRegistry
{
    /** @var LayoutInterface[] */
    protected $layouts = [];

    /**
     * LayoutRegistry constructor.
     * @TODO remove dependency by merging it in this method
     * @param LayoutFactory $layoutFactory
     */
    public function __construct(LayoutFactory $layoutFactory)
    {
        $layoutFactory->setLayoutRegistry($this);
    }


    /**
     * @return LayoutInterface[]
     */
    public function getLayouts() : array
    {
        return $this->layouts;
    }

    /**
     * @param LayoutInterface $layout
     */
    public function addLayout(LayoutInterface $layout)
    {
        $this->layouts[$layout->getCode()] = $layout;
    }

    /**
     * @param string $layoutCode
     *
     * @throws MissingLayoutException
     *
     * @return LayoutInterface
     */
    public function getLayout(string $layoutCode) : LayoutInterface
    {
        if (!$this->hasLayout($layoutCode)) {
            throw MissingLayoutException::create($layoutCode);
        }

        return $this->layouts[$layoutCode];
    }

    /**
     * @param string $layoutCode
     *
     * @return bool
     */
    public function hasLayout(string $layoutCode) : bool
    {
        return array_key_exists($layoutCode, $this->layouts);
    }
}
