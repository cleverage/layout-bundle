<?php

namespace CleverAge\LayoutBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\NodeBuilder;
use Symfony\Component\Config\Definition\Builder\NodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files.
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/configuration.html}
 */
class Configuration implements ConfigurationInterface
{
    /** @var string */
    protected $root;

    /**
     * @param string $root
     */
    public function __construct($root = 'clever_age_layout')
    {
        $this->root = $root;
    }

    /**
     * {@inheritdoc}
     *
     * @throws \RuntimeException
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root($this->root);

        $rootNode
            ->children()
            ->append($this->getLayoutsConfigTreeBuilder())
            ->append($this->getParametersNodeTreeBuilder())
            ->end();

        return $treeBuilder;
    }

    /**
     * @throws \RuntimeException
     *
     * @return ArrayNodeDefinition|NodeDefinition
     */
    protected function getParametersNodeTreeBuilder()
    {
        $builder = new TreeBuilder();
        $node = $builder->root('parameters');
        $node->useAttributeAsKey('code')->prototype('variable');

        return $node;
    }

    /**
     * @throws \RuntimeException
     *
     * @return NodeDefinition
     */
    protected function getLayoutsConfigTreeBuilder()
    {
        $builder = new TreeBuilder();
        $node = $builder->root('layouts');
        $layoutDefinition = $node
            ->useAttributeAsKey('code')
            ->prototype('array')
            ->performNoDeepMerging()
            ->cannotBeOverwritten()
            ->children();

        $this->appendLayoutDefinition($layoutDefinition);

        $layoutDefinition->end()
            ->end()
            ->end();

        return $node;
    }

    /**
     * @param NodeBuilder $layoutDefinition
     */
    protected function appendLayoutDefinition(NodeBuilder $layoutDefinition)
    {
        /** @var ArrayNodeDefinition $slotArrayNodeDefinition */
        $slotArrayNodeDefinition = $layoutDefinition
            ->scalarNode('template')->isRequired()->end()
            ->scalarNode('parent')->end()
            ->variableNode('global_parameters')->end()
            ->arrayNode('slots')
            ->useAttributeAsKey('code')
            ->prototype('array');

        $blockDefinition = $slotArrayNodeDefinition
            ->performNoDeepMerging()
            ->prototype('array')
            ->children();

        $this->appendBlockDefinition($blockDefinition);

        $blockDefinition->end()
            ->end()
            ->end()
            ->end()
            ->end();
    }

    /**
     * @param NodeBuilder $blockDefinition
     */
    protected function appendBlockDefinition(NodeBuilder $blockDefinition)
    {
        $blockDefinition
            ->scalarNode('block_code')->end()
            ->variableNode('parameters')->end()
            ->booleanNode('displayed')->end()
            ->scalarNode('after')->end()
            ->scalarNode('before')->end();
    }
}