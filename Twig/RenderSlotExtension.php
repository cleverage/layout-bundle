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
use CleverAge\LayoutBundle\Layout\Layout;

/**
 * Render a slot using layout configuration
 * Optionally wraps the HTML into a debug div
 */
class RenderSlotExtension extends \Twig_Extension implements \Twig_Extension_InitRuntimeInterface
{

    /** @var  \Twig_Environment */
    protected $twigEnv;

    /**
     * Get the reference to Twig environment
     *
     * @param \Twig_Environment $environment
     */
    public function initRuntime(\Twig_Environment $environment)
    {
        $this->twigEnv = $environment;
    }

    /**
     * Defines the new twig functions
     *
     * @return array
     */
    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('render_slot', [$this, 'renderSlot'], ['is_safe' => ['html']]),
            new \Twig_SimpleFunction('has_blocks', [$this, 'hasBlocks']),
        ];
    }

    /**
     * @param Layout $layout
     * @param string $slotCode
     *
     * @throws MissingSlotException
     * @throws MissingBlockException
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     *
     * @return string
     */
    public function renderSlot(Layout $layout, string $slotCode): string
    {
        $slotHtml = $this->getSlotHtml($layout, $slotCode);

        return $this->wrapHtml($layout, $slotHtml, ['type' => 'slot', 'code' => $slotCode]);
    }

    /**
     * @param Layout $layout
     * @param string[] ...$slotCodes
     *
     * @throws MissingException
     *
     * @return bool
     */
    public function hasBlocks(Layout $layout, string ...$slotCodes)
    {
        return $layout->getSlotBlockCount(...$slotCodes) > 0;
    }

    /**
     * Alias to the renderSlot method of the layout manager
     *
     * @param Layout $layout
     * @param string $slotCode
     *
     * @throws MissingSlotException
     * @throws MissingBlockException
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     *
     * @return string
     */
    public function getSlotHtml(Layout $layout, string $slotCode): string
    {
        $blocksHtml = $layout->getBlocksHtml($slotCode);
        $html = '';
        foreach ($blocksHtml as $code => $blockHtml) {
            $html .= $this->wrapHtml($layout, $blockHtml, ['type' => 'block', 'code' => $code]);
        }

        return $html;
    }

    /**
     * @param Layout $layout
     * @param string $html
     * @param array $options
     *
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     *
     * @return string
     */
    protected function wrapHtml(Layout $layout, string $html, array $options): string
    {
        if ($layout->getDebugMode()) {
            $html = $this->twigEnv->render(
                'CleverAgeLayoutBundle::debug.html.twig',
                [
                    'content' => $html,
                    'type' => $options['type'],
                    'code' => $options['code'],
                    'debug_mode' => $layout->getDebugMode(),
                ]
            );
        }

        return $html;
    }


}
