<?php

namespace CleverAge\LayoutBundle\Block;

use Symfony\Component\HttpFoundation\Request;

/**
 * Handles cache initialization and revokation
 */
interface CacheableBlockInterface extends BlockInterface
{
    /**
     * Should only add elements to the "Request Cacheable Attributes" array ; they are used to determine caching
     * To implement in children :
     *  - add request tag
     *  - add parameter tag
     *  - set cache lifetime
     *
     * @param Request $request
     * @param array   $parameters
     */
    public function handleRequest(Request $request, array $parameters = []): void;

    /**
     * Should do the classic initialization work, and also define entity-related tags
     *
     * @param Request $request
     * @param array   $parameters
     */
    public function handleInitialization(Request $request, array $parameters = []): void;
}
