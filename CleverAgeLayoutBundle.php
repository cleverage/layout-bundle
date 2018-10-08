<?php
/*
 * This file is part of the CleverAge/LayoutBundle package.
 *
 * Copyright (c) 2015-2018 Clever-Age
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CleverAge\LayoutBundle;

use CleverAge\LayoutBundle\Block\BlockRegistry;
use Sidus\BaseBundle\DependencyInjection\Compiler\GenericCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use CleverAge\LayoutBundle\Layout\LayoutRegistry;

/**
 * Class CleverAgeLayoutBundle
 */
class CleverAgeLayoutBundle extends Bundle
{
    /**
     * Adding compiler passes to inject services into configuration handlers
     *
     * @param ContainerBuilder $container
     */
    public function build(ContainerBuilder $container): void
    {
        $container->addCompilerPass(
            new GenericCompilerPass(
                BlockRegistry::class,
                'clever.block',
                'addBlock'
            )
        );
        $container->addCompilerPass(
            new GenericCompilerPass(
                LayoutRegistry::class,
                'clever.layout',
                'addLayout'
            )
        );
    }
}
