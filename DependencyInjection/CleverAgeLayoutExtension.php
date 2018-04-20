<?php

namespace CleverAge\LayoutBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration.
 *
 * @link http://symfony.com/doc/current/cookbook/bundles/extension.html
 */
class CleverAgeLayoutExtension extends Extension
{
    /**
     * {@inheritdoc}
     * @throws \Exception
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config/services'));
        $loader->load('factory.yml');
        $loader->load('registry.yml');
        $loader->load('twig.yml');
        $loader->load('event.yml');

        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $layoutFactoryDefinition = $container->getDefinition('clever_age_layout.factory.layout');
        $layoutFactoryDefinition->addMethodCall('parseConfiguration', [$config]);
    }
}