<?php
/*
 * This file is part of the CleverAge/LayoutBundle package.
 *
 * Copyright (c) 2015-2018 Clever-Age
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CleverAge\LayoutBundle\DependencyInjection;

use CleverAge\LayoutBundle\Registry\LayoutRegistry;
use Sidus\BaseBundle\DependencyInjection\SidusBaseExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\Config\FileLocator;

/**
 * This is the class that loads and manages your bundle configuration.
 *
 * @link http://symfony.com/doc/current/cookbook/bundles/extension.html
 */
class CleverAgeLayoutExtension extends SidusBaseExtension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container): void
    {
        parent::load($configs, $container);

        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $layoutRegistryDefinition = $container->getDefinition(LayoutRegistry::class);
        $layoutRegistryDefinition->addMethodCall('parseConfiguration', [$config]);

        if ($config['debug_mode'] ?? false) {
            $refl = new \ReflectionClass($this); // Supports for class extending this one
            $path = \dirname($refl->getFileName(), 2).'/Resources/config';
            $loader = new YamlFileLoader($container, new FileLocator($path));
            $loader->load('debug.yml');
        }
    }
}
