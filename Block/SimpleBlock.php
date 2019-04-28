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

use function array_merge;
use CleverAge\LayoutBundle\Event\BlockInitializationEvent;

/**
 * Generic block to render a twig template without any business logic
 */
class SimpleBlock implements BlockInterface
{
    /** @var string */
    protected $code;

    /** @var string */
    protected $template;

    /** @var array */
    protected $templateParameters = [];

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
     * {@inheritDoc}
     */
    public function initialize(BlockInitializationEvent $event): void
    {
        $this->templateParameters = array_merge(
            $event->getLayout()->getGlobalParameters(),
            $event->getBlockDefinition()->getParameters(),
            $event->getTemplateParameters(),
            $event->getControllerResponse(),
            [
                '_layout' => $event->getLayout(),
                '_slot' => $event->getSlot(),
                '_block_definition' => $event->getBlockDefinition(),
                '_block' => $this,
            ]
        );
    }

    /**
     * @return string
     */
    public function getTemplate(): string
    {
        return $this->template;
    }

    /**
     * Return the
     *
     * @return array
     */
    public function getTemplateParameters(): array
    {
        return $this->templateParameters;
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
