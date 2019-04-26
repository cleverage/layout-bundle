<?php
/*
 * This file is part of the CleverAge/LayoutBundle package.
 *
 * Copyright (c) 2015-2018 Clever-Age
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use CleverAge\LayoutBundle\Registry\BlockRegistry;

/**
 * @deprecated use AdminLinkType instead
 */
class_alias(BlockRegistry::class, 'CleverAge\LayoutBundle\Block\BlockRegistry');

@trigger_error(
    'CleverAge\LayoutBundle\Block\BlockRegistry is deprecated, move to CleverAge\LayoutBundle\Registry\BlockRegistry instead',
    E_USER_DEPRECATED
);
