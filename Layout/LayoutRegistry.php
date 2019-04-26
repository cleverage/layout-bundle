<?php
/*
 * This file is part of the CleverAge/LayoutBundle package.
 *
 * Copyright (c) 2015-2018 Clever-Age
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use CleverAge\LayoutBundle\Registry\LayoutRegistry;

/**
 * @deprecated use AdminLinkType instead
 */
class_alias(LayoutRegistry::class, 'CleverAge\LayoutBundle\Layout\LayoutRegistry');

@trigger_error(
    'CleverAge\LayoutBundle\Layout\LayoutRegistry is deprecated, move to CleverAge\LayoutBundle\Registry\LayoutRegistry instead',
    E_USER_DEPRECATED
);
