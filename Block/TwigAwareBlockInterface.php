<?php

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
