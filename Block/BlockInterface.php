<?php

namespace CleverAge\LayoutBundle\Block;

use Symfony\Component\HttpFoundation\Request;

/**
 * Represents the base interface for the block services that are used to render a given layout
 * Data shared between blocks should use a common service
 */
interface BlockInterface
{

    /**
     * Set the block data depending on the current request
     *
     * @param Request $request
     * @param array   $parameters
     */
    public function initialize(Request $request, array $parameters = []);

    /**
     * Render the twig template associated to the block, and return the HTML
     *
     * @param array $parameters
     *
     * @return string
     */
    public function render(array $parameters = []): string;

    /**
     * Get the code of the block
     *
     * @return string
     */
    public function getCode(): string;
}