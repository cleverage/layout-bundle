<?php

namespace CleverAge\LayoutBundle\Block;

use Symfony\Component\Templating\EngineInterface;

/**
 * Allow automatic Twig injection
 */
interface RendererAwareBlockInterface
{
    /**
     * @param EngineInterface $renderer
     */
    public function setRenderer(EngineInterface $renderer);
}
