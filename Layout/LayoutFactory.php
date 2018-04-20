<?php

namespace CleverAge\LayoutBundle\Layout;

use CleverAge\LayoutBundle\Block\BlockRegistry;
use Symfony\Component\Stopwatch\Stopwatch;

/**
 * Merge configurations and creates layouts from yml config
 *
 * @TODO merge this class inside the registry to avoid reversed DI
 */
class LayoutFactory
{
    /** @var LayoutRegistry */
    protected $layoutRegistry;

    /** @var BlockRegistry */
    protected $blockRegistry;

    /** @var Stopwatch */
    protected $stopwatch;

    /**
     * Due to reversed DI, registries are created after this factory
     * This variable holds data, waiting for registry creation.
     *
     * @var array
     */
    protected $pendingBlocks = [];

    /**
     * LayoutFactory constructor.
     *
     * @param BlockRegistry  $blockRegistry
     * @param Stopwatch|null $stopwatch
     */
    public function __construct(BlockRegistry $blockRegistry, Stopwatch $stopwatch = null)
    {
        $this->blockRegistry = $blockRegistry;
        $this->stopwatch = $stopwatch;
    }

    /**
     * @param LayoutRegistry $layoutRegistry
     */
    public function setLayoutRegistry(LayoutRegistry $layoutRegistry)
    {
        $this->layoutRegistry = $layoutRegistry;
        $this->updateRegistries();
    }

    /**
     * Read a configuration and prepare layout and block creation
     *
     * @param array $configuration
     */
    public function parseConfiguration(array $configuration)
    {
        $debugMode = false;
        if (array_key_exists('parameters', $configuration)
            && array_key_exists('debug_mode', $configuration['parameters'])
        ) {
            $debugMode = $configuration['parameters']['debug_mode'];
        }

        $layoutCodes = array_keys($configuration['layouts']);
        foreach ($layoutCodes as $layoutCode) {
            $layoutConfig = $this->getMergedLayout($configuration, $layoutCode);

            $layoutTemplate = $this->parseDefault($layoutConfig, 'template', '');
            $layoutSlotsDefinition = $this->parseDefault($layoutConfig, 'slots', []);
            $layoutSlots = [];
            foreach ($layoutSlotsDefinition as $slotCode => $slotDefinition) {
                $slot = new Slot($slotCode);
                foreach ($slotDefinition as $blockDefinitionCode => $blockDefinitionConfig) {
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
                'debug_mode' => $debugMode,
            ];
        }

        $this->updateRegistries();
    }

    /**
     * Flush the pendingBlocks array to fill blockRegistry and layoutRegistry
     */
    protected function updateRegistries()
    {
        if ($this->blockRegistry && $this->layoutRegistry) {
            foreach ($this->pendingBlocks as $block) {
                $layout = new Layout(
                    $this->blockRegistry,
                    $block['layout_code'],
                    $block['layout_template'],
                    $block['layout_slots'],
                    $block['layout_params']
                );

                if ($this->stopwatch) {
                    $layout->setStopwatch($this->stopwatch);
                }

                if ($block['debug_mode']) {
                    $layout->setDebugMode($block['debug_mode']);
                }

                $this->layoutRegistry->addLayout($layout);
            }
            $this->pendingBlocks = [];
        }
    }

    /**
     * @param array  $configuration
     * @param string $layoutName
     *
     * @return array
     */
    protected function getMergedLayout($configuration, $layoutName)
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
