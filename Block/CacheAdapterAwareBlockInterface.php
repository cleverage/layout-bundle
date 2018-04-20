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
