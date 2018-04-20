<?php
/*
 * This file is part of the CleverAge/LayoutBundle package.
 *
 * Copyright (c) 2015-2018 Clever-Age
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CleverAge\LayoutBundle\Block;

/**
 * Allow automatic Twig injection
 */
interface TwigAwareBlockInterface
{

    /**
     * @param \Twig_Environment $twig
     */
    public function setTwig(\Twig_Environment $twig);
}
