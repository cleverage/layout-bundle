<?php
/*
 * This file is part of the CleverAge/LayoutBundle package.
 *
 * Copyright (c) 2015-2018 Clever-Age
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CleverAge\LayoutBundle\Tests\Layout;

use CleverAge\LayoutBundle\DependencyInjection\CleverAgeLayoutExtension;
use CleverAge\LayoutBundle\Layout\LayoutInterface;
use CleverAge\LayoutBundle\Registry\LayoutRegistry;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Main tests of layout configuration
 */
class ConfigurationTest extends TestCase
{
    /** @var  ContainerBuilder */
    protected $container;

    public const CONFIG_SAMPLE_LAYOUTS = [
        'clever_age_layout' => [
            'layouts' => [
                'empty_layout' => [
                    'template' => 'not-a-template.html.twig',
                    'slots' => [],
                ],
                'simple_layout' => [
                    'template' => 'not-a-template.html.twig',
                    'slots' => [
                        'main' => [
                            'block_1' => null,
                            'block_2' => [
                                'block_code' => null,
                            ],
                            'block_3' => [
                                'block_code' => 'custom_block_code',
                            ],
                            'test_block_code' => [
                                'block_code' => 'block_2',
                            ],
                        ],
                    ],
                ],
                'inheriting_layout' => [
                    'parent' => 'simple_layout',
                    'template' => 'template2.html.twig',
                    'slots' => [
                        'main' => [
                            // Simple relative positioning
                            'block_4' => [
                                'after' => 'block_1',
                            ],
                            'block_5' => [
                                'before' => 'block_3',
                            ],

                            // Chained relative positioning, with dependencies in the wrong order
                            'block_6' => [
                                'after' => 'block_7',
                            ],
                            'block_7' => [
                                'before' => 'block_4',
                            ],

                            // Absolute positioning
                            'block_8' => [
                                'after' => '*',
                            ],
                            'block_9' => [
                                'before' => '*',
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ];

    /**
     * Initialize DI
     */
    protected function setUp()
    {
        parent::setUp();
        $this->container = $this->getContainerBuilder();
    }

    /**
     * Test the default behavior (without defined layout)
     *
     * @throws \Exception
     */
    public function testDefault(): void
    {
        self::assertCount(0, $this->getLayoutRegistry()->getLayouts());
    }

    /**
     * Test the creation of an empty Layout
     *
     * @throws \Exception
     */
    public function testEmptyLayout(): void
    {
        $layout = $this->getSampleLayout('empty_layout');

        self::assertEquals('empty_layout', $layout->getCode());
        self::assertEquals('not-a-template.html.twig', $layout->getTemplate());
    }

    /**
     * Test a simple layout (without inheritance)
     *
     * @throws \Exception
     */
    public function testSimpleLayout(): void
    {
        $layout = $this->getSampleLayout('simple_layout');
        $mainSlot = $layout->getSlot('main');

        self::assertCount(4, $mainSlot->getBlockDefinitions());
        self::assertEquals('block_1', $mainSlot->getBlockDefinition('block_1')->getBlockCode());
        self::assertTrue($mainSlot->getBlockDefinition('block_1')->isDisplayed());

        self::assertEquals('block_2', $mainSlot->getBlockDefinition('block_2')->getBlockCode());
        self::assertEquals('custom_block_code', $mainSlot->getBlockDefinition('block_3')->getBlockCode());
    }

    /**
     * Test inheritance between layouts
     *
     * @throws \Exception
     */
    public function testInheritance(): void
    {
        $layout = $this->getSampleLayout('inheriting_layout');
        self::assertEquals('template2.html.twig', $layout->getTemplate());

        $mainSlot = $layout->getSlot('main');
        self::assertCount(10, $mainSlot->getBlockDefinitions());

        // Retest inherited blocks
        self::assertEquals('block_1', $mainSlot->getBlockDefinition('block_1')->getBlockCode());
        self::assertEquals('block_2', $mainSlot->getBlockDefinition('block_2')->getBlockCode());
        self::assertEquals('custom_block_code', $mainSlot->getBlockDefinition('block_3')->getBlockCode());
        self::assertEquals('block_2', $mainSlot->getBlockDefinition('test_block_code')->getBlockCode());
    }

    /**
     * Test relative, chained and absolute positioning
     *
     * @throws \Exception
     */
    public function testPositioning(): void
    {
        $layout = $this->getSampleLayout('inheriting_layout');
        $mainSlot = $layout->getSlot('main');
        $blocks = array_keys($mainSlot->getBlockDefinitions());

        self::assertEquals(
            [
                'block_9',
                'block_1',
                'block_7',
                'block_6',
                'block_4',
                'block_2',
                'block_5',
                'block_3',
                'test_block_code',
                'block_8',
            ],
            $blocks
        );
    }

    /**
     * Load and get one the sample layout
     *
     * @param string $code
     *
     * @throws \Exception
     *
     * @return LayoutInterface
     */
    protected function getSampleLayout(string $code): LayoutInterface
    {
        $registry = $this->getLayoutRegistry(self::CONFIG_SAMPLE_LAYOUTS);

        return $registry->getLayout($code);
    }

    /**
     * Get the full layout registry
     *
     * @param array $config
     *
     * @throws \Exception
     *
     * @return LayoutRegistry
     */
    protected function getLayoutRegistry(array $config = []): LayoutRegistry
    {
        $this->loadConfiguration($config);

        return $this->container->get(LayoutRegistry::class);
    }

    /**
     * Loads the given configuration using the Extension
     *
     * @param array $config
     *
     * @throws \Exception
     */
    protected function loadConfiguration(array $config = []): void
    {
        $this->getExtension()->load($config, $this->container);
    }

    /**
     * Generates a new instance of the bundle extension
     *
     * @return CleverAgeLayoutExtension
     */
    protected function getExtension(): CleverAgeLayoutExtension
    {
        return new CleverAgeLayoutExtension();
    }

    /**
     * Generates a new instance of the container builder
     *
     * @return ContainerBuilder
     */
    protected function getContainerBuilder(): ContainerBuilder
    {
        return new ContainerBuilder();
    }
}
