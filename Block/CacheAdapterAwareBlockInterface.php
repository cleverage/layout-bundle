<?php

namespace CleverAge\LayoutBundle\Block;

use Symfony\Component\Cache\Adapter\AdapterInterface;

/**
 * Allow automatic CacheAdapter injection
 */
interface CacheAdapterAwareBlockInterface
{
    /**
     * @param AdapterInterface $cacheAdapter
     */
    public function setCacheAdapter(AdapterInterface $cacheAdapter);

    /**
     * @param bool $enableCache
     */
    public function setEnableCache(bool $enableCache);
}
