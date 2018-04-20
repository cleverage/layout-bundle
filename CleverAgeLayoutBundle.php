<?php

namespace CleverAge\LayoutBundle;

use CleverAge\LayoutBundle\DependencyInjection\Compiler\GenericCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class CleverAgeLayoutBundle extends Bundle
{
    /**
     * Adding compiler passes to inject services into configuration handlers
     *
     * @param ContainerBuilder $container
     */
    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new GenericCompilerPass(
            'clever_age_layout.registry.block',
            'clever.block',
            'addBlock'
        ));
        $container->addCompilerPass(new GenericCompilerPass(
            'clever_age_layout.registry.layout',
            'clever.layout',
            'addLayout'
        ));
    }
}
