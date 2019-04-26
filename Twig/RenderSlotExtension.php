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

use CleverAge\LayoutBundle\Exception\MissingBlockException;
use CleverAge\LayoutBundle\Exception\MissingException;
use CleverAge\LayoutBundle\Exception\MissingSlotException;
use CleverAge\LayoutBundle\Layout\LayoutInterface;
use CleverAge\LayoutBundle\Templating\SlotRendererInterface;
use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * Render a slot using layout configuration
 * Optionally wraps the HTML into a debug div
 */
class RenderSlotExtension extends AbstractExtension
{
    /** @var Environment */
    protected $twig;

    /** @var SlotRendererInterface */
    protected $slotRenderer;

    /** @var bool */
    protected $debugMode = false;

    /**
     * @param Environment           $twig
     * @param SlotRendererInterface $slotRenderer
     * @param bool                  $debugMode
     */
    public function __construct(Environment $twig, SlotRendererInterface $slotRenderer, bool $debugMode = false)
    {
        $this->twig = $twig;
        $this->slotRenderer = $slotRenderer;
        $this->debugMode = $debugMode;
    }

    /**
     * Defines the new twig functions
     *
     * @return array
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('render_slot', [$this, 'renderSlot'], ['is_safe' => ['html']]),
            new TwigFunction('has_blocks', [$this, 'hasBlocks']),
        ];
    }

    /**
     * @param LayoutInterface $layout
     * @param string          $slotCode
     *
     * @return string
     */
    public function renderSlot(LayoutInterface $layout, string $slotCode): string
    {
        return $this->wrapHtml($this->getSlotHtml($layout, $slotCode), 'slot', $slotCode);
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
            if (!$this->slotRenderer->isEmptySlot($layout, $slotCode)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Alias to the renderSlot method of the layout manager
     *
     * @param LayoutInterface $layout
     * @param string          $slotCode
     *
     * @throws MissingSlotException
     * @throws MissingBlockException
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     *
     * @return string
     */
    public function getSlotHtml(LayoutInterface $layout, string $slotCode): string
    {
        $blocksHtml = $this->slotRenderer->renderSlot($layout, $slotCode);
        $html = '';
        foreach ($blocksHtml as $code => $blockHtml) {
            $html .= $this->wrapHtml($blockHtml, 'block', $code);
        }

        return $html;
    }

    /**
     * @param string $html
     * @param string $type
     * @param string $code
     *
     * @return string
     */
    protected function wrapHtml(string $html, string $type, string $code): string
    {
        if ($this->debugMode) {
            $html = $this->twig->render(
                'CleverAgeLayoutBundle::debug.html.twig',
                [
                    'content' => $html,
                    'type' => $type,
                    'code' => $code,
                    'debug_mode' => 1,
                ]
            );
        }

        return $html;
    }
}
