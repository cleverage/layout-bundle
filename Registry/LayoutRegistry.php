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

use CleverAge\LayoutBundle\Layout\BlockDefinition;
use CleverAge\LayoutBundle\Layout\Layout;
use CleverAge\LayoutBundle\Layout\LayoutInterface;
use CleverAge\LayoutBundle\Exception\MissingLayoutException;
use CleverAge\LayoutBundle\Layout\Slot;

/**
 * Holds all the layout services, automatically injected through the clever.layout tag
 */
class LayoutRegistry
{
    /** @var LayoutInterface[] */
    protected $layouts = [];

    /**
     * Due to reversed DI, registries are created after this factory
     * This variable holds data, waiting for registry creation.
     *
     * @var array
     */
    protected $pendingBlocks = [];

    /**
     */
    public function __construct()
    {
        $this->updateRegistries();
    }


    /**
     * @return LayoutInterface[]
     */
    public function getLayouts(): array
    {
        return $this->layouts;
    }

    /**
     * @param LayoutInterface $layout
     */
    public function addLayout(LayoutInterface $layout): void
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
    public function getLayout(string $layoutCode): LayoutInterface
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
    public function hasLayout(string $layoutCode): bool
    {
        return array_key_exists($layoutCode, $this->layouts);
    }

    /**
     * Read a configuration and prepare layout and block creation
     *
     * @param array $configuration
     */
    public function parseConfiguration(array $configuration): void
    {
        $layoutCodes = array_keys($configuration['layouts']);
        foreach ($layoutCodes as $layoutCode) {
            $layoutConfig = $this->getMergedLayout($configuration, $layoutCode);

            $layoutTemplate = $this->parseDefault($layoutConfig, 'template', '');
            $layoutSlotsDefinition = $this->parseDefault($layoutConfig, 'slots', []);
            $layoutSlots = [];
            foreach ($layoutSlotsDefinition as $slotCode => $slotDefinition) {
                $slot = new Slot($slotCode);
                foreach ($slotDefinition as $blockDefinitionCode => $blockDefinitionConfig) {
                    /** @noinspection PhpUnhandledExceptionInspection */
                    $blockDefinition = new BlockDefinition($blockDefinitionCode, $blockDefinitionConfig);
                    $slot->addBlockDefinition($blockDefinition);
                }
                $layoutSlots[$slotCode] = $slot;
            }
            $globalParameters = $this->parseDefault($layoutConfig, 'global_parameters', []);
            $this->pendingBlocks[] = [
                'layout_code' => $layoutCode,
                'layout_template' => $layoutTemplate,
                'layout_slots' => $layoutSlots,
                'layout_params' => $globalParameters,
            ];
        }

        $this->updateRegistries();
    }

    /**
     * Flush the pendingBlocks array to fill blockRegistry and layoutRegistry
     */
    protected function updateRegistries(): void
    {
        foreach ($this->pendingBlocks as $block) {
            $layout = new Layout(
                $block['layout_code'],
                $block['layout_template'],
                $block['layout_slots'],
                $block['layout_params']
            );

            $this->addLayout($layout);
        }
        $this->pendingBlocks = [];
    }

    /**
     * @param array  $configuration
     * @param string $layoutName
     *
     * @return array
     */
    protected function getMergedLayout($configuration, $layoutName): array
    {
        $layoutConfig = $configuration['layouts'][$layoutName];
        $parentLayout = $this->parseDefault($layoutConfig, 'parent');
        if ($parentLayout) {
            $parentConfig = $this->getMergedLayout($configuration, $parentLayout);
            $layoutConfig = array_replace_recursive($parentConfig, $layoutConfig);
        }

        return $layoutConfig;
    }

    /**
     * @param array  $layoutConfig
     * @param string $key
     * @param mixed  $default
     *
     * @return mixed
     */
    protected function parseDefault($layoutConfig, $key, $default = null)
    {
        return array_key_exists($key, $layoutConfig) ? $layoutConfig[$key] : $default;
    }
}
