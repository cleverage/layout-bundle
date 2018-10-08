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

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Templating\EngineInterface;

/**
 * Generic block to render a twig template without any business logic
 */
class SimpleBlock implements BlockInterface, RendererAwareBlockInterface
{
    /** @var EngineInterface */
    protected $twig;

    /** @var string */
    protected $template;

    /** @var string */
    protected $code;

    /**
     * @param string $code
     * @param string $template
     */
    public function __construct(string $code, string $template)
    {
        $this->template = $template;
        $this->code = $code;
    }

    /**
     * @param EngineInterface $renderer
     */
    public function setRenderer(EngineInterface $renderer): void
    {
        $this->twig = $renderer;
    }

    /**
     * {@inheritdoc}
     */
    public function initialize(Request $request, array $parameters = []): void
    {
        // Do nothing by default
    }

    /**
     * Render the twig template associated to the block, and return the HTML
     *
     * @param array $parameters
     *
     * @return string
     */
    public function render(array $parameters = []): string
    {
        $parameters['_block'] = $this;

        return $this->twig->render($this->template, $parameters);
    }

    /**
     * Get the code of the block
     *
     * @return string
     */
    public function getCode(): string
    {
        return $this->code;
    }
}
