<?php
/*
 * This file is part of the CleverAge/LayoutBundle package.
 *
 * Copyright (c) 2015-2018 Clever-Age
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CleverAge\LayoutBundle\Twig;

use CleverAge\LayoutBundle\Exception\MissingException;
use CleverAge\LayoutBundle\Layout\LayoutInterface;
use CleverAge\LayoutBundle\Templating\SlotRendererInterface;
use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * Render a slot using layout configuration
 */
class RenderSlotExtension extends AbstractExtension
{
    /** @var Environment */
    protected $twig;

    /** @var SlotRendererInterface */
    protected $slotRenderer;

    /**
     * @param Environment           $twig
     * @param SlotRendererInterface $slotRenderer
     */
    public function __construct(Environment $twig, SlotRendererInterface $slotRenderer)
    {
        $this->twig = $twig;
        $this->slotRenderer = $slotRenderer;
    }

    /**
     * Defines the new twig functions
     *
     * @return array
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('render_slot', [$this->slotRenderer, 'renderSlot'], ['is_safe' => ['html']]),
            new TwigFunction('has_blocks', [$this, 'hasBlocks']),
        ];
    }

    /**
     * @param LayoutInterface $layout
     * @param string|string[] $slotCodes
     *
     * @throws MissingException
     *
     * @return bool
     */
    public function hasBlocks(LayoutInterface $layout, $slotCodes): bool
    {
        foreach ((array) $slotCodes as $slotCode) {
            if (!empty(trim($this->slotRenderer->renderSlot($layout, $slotCode)))) {
                return true;
            }
        }

        return false;
    }
}
