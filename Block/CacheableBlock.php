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

use Cocur\Slugify\Slugify;
use Symfony\Component\Cache\Adapter\AdapterInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * A basic implementation of a cacheable block
 * Unless forced otherwise, there is cache only on blocks that use any kind of tags
 */
class CacheableBlock extends SimpleBlock implements CacheAdapterAwareBlockInterface
{
    /** @var Slugify */
    protected $slugifier;

    /** @var string[] */
    protected $cacheTags = [];

    /** @var AdapterInterface */
    protected $cacheAdapter;

    /** @var \DateInterval */
    protected $cacheLifetime;

    /** @var \DateTime */
    protected $cacheExpiresAt;

    /** @var string[] */
    protected $cacheKeys = [];

    /** @var CacheableBlock[] */
    protected $cacheDependencies = [];

    // Cache control

    /** @var bool */
    protected $forceInit = false;

    /** @var bool */
    protected $forceRender = false;

    /**
     * Application wide switch
     *
     * @var bool
     */
    protected $enableCache = true;

    /**
     * @param string $code
     * @param string $template
     */
    public function __construct($code, $template)
    {
        parent::__construct($code, $template);

        $this->slugifier = Slugify::create(
            [
                'lowercase' => false,
                'regexp' => '/([^A-Za-z0-9%_=]|-)+/',
            ]
        );
    }


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
    public function handleRequest(Request $request, array $parameters = [])
    {
        // Prepare caching parameters here
        // Remove the flag to enable cache
        $this->forceInit = true;
    }

    /**
     * By default, the block is fully cached if the request and parameters does not change
     *
     * {@inheritDoc}
     */
    final public function initialize(Request $request, array $parameters = [])
    {
        $this->addTag('BLOCK_'.$this->getCode());
        $this->handleRequest($request, $parameters);

        if (!$this->enableCache || ($parameters['skip_init_cache'] ?? $this->forceInit)) {
            $this->handleInitialization($request, $parameters);

            return null;
        }

        $this->cacheKeys[] = $this->getCacheKey();
        if (!$this->hasRecursiveCache()) {
            $this->handleInitialization($request, $parameters);
        }
    }

    /**
     * Should do the classic initialization work, and also define entity-related tags
     *
     * @param Request $request
     * @param array   $parameters
     */
    public function handleInitialization(Request $request, array $parameters = [])
    {
        // Implement here classic bloc init
        // Remove the flag to enable cache
        $this->forceRender = true;
    }


    /**
     * {@inheritDoc}
     * @throws \Symfony\Component\Cache\Exception\InvalidArgumentException
     * @throws \Psr\Cache\InvalidArgumentException
     * @throws \UnexpectedValueException
     */
    final public function render(array $parameters = []): string
    {
        if (!$this->forceInit && $this->forceRender) {
            throw new \UnexpectedValueException('Rendering cannot be forced if init has been skipped');
        }

        if (!$this->enableCache || ($parameters['skip_render_cache'] ?? $this->forceRender)) {
            return $this->handleRendering($parameters);
        }

        $this->cacheKeys[] = $this->getCacheKey();

        // There can be multiple keys that uses the same tags :
        // it will be useful for init caching & still correct invalidation
        $html = null;
        foreach ($this->cacheKeys as $cacheKey) {
            $result = $this->cacheAdapter->getItem($cacheKey);
            if ($result->isHit()) {
                return $result->get();
            }
            if (!isset($html)) {
                $html = $this->handleRendering($parameters);
            }
            $result->set($html);
            $result->tag($this->cacheTags);
            if (isset($this->cacheLifetime)) {
                $result->expiresAfter($this->cacheLifetime);
            }
            if (isset($this->cacheExpiresAt)) {
                $result->expiresAt($this->cacheExpiresAt);
            }

            $this->cacheAdapter->save($result);
        }

        return $html;
    }

    /**
     * Override-able rendering method
     *
     * @param array $parameters
     *
     * @return string
     * @throws \Twig_Error
     */
    public function handleRendering(array $parameters = []): string
    {
        return parent::render($parameters);
    }

    /**
     * Recursively check if cache is available for a block and its children block
     *
     * @return bool
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function hasRecursiveCache(): bool
    {
        $key = $this->getCacheKey();

        if ($key) {
            $result = $this->cacheAdapter->getItem($key);

            if ($result->isHit()) {
                $childrenCache = true;
                foreach ($this->cacheDependencies as $cacheDependency) {
                    $childrenCache = $childrenCache && $cacheDependency->hasRecursiveCache();
                }

                return $childrenCache;
            }
        }

        return false;
    }

    /**
     * @param AdapterInterface $cacheAdapter
     */
    public function setCacheAdapter(AdapterInterface $cacheAdapter)
    {
        $this->cacheAdapter = $cacheAdapter;
    }

    /**
     * @param bool $enableCache
     */
    public function setEnableCache(bool $enableCache)
    {
        $this->enableCache = $enableCache;
    }

    /**
     * @param BlockInterface $block
     *
     * @throws \UnexpectedValueException
     */
    protected function addCacheDependency(BlockInterface $block)
    {
        if (!$block instanceof CacheableBlock) {
            throw new \UnexpectedValueException('A dependent block must be cacheable');
        }

        $this->cacheDependencies[$block->getCode()] = $block;

        // TODO find a way to set tags of all blocks as interdependent...
        // For now, each block must be self-aware of linked tags
    }

    /**
     * @param string $tagName
     */
    protected function addTag(string $tagName)
    {
        $tagName = $this->slugifier->slugify($tagName);
        if (!in_array($tagName, $this->cacheTags, true)) {
            $this->cacheTags[] = $tagName;
        }
    }

    /**
     * @param Request $request
     * @param string  $requestAttribute
     */
    protected function addRequestTag(Request $request, string $requestAttribute)
    {
        if ($request->get($requestAttribute)) {
            $value = $request->get($requestAttribute);
            $this->addTag('REQ_'.$requestAttribute.'='.urlencode($value));
        }
    }

    /**
     * @param array  $parameter
     * @param string $key
     */
    protected function addParameterTag(array $parameter, string $key)
    {
        $this->forceRender = false;
        $this->forceInit = false;
        if (array_key_exists($key, $parameter)) {
            $this->addTag('PARAM_'.$key.'='.$parameter[$key]);
        }
    }

    /**
     * @param array $tagNames
     */
    protected function addTags(array $tagNames)
    {
        foreach ($tagNames as $tagName) {
            $this->addTag($tagName);
        }
    }

    /**
     * @param Request $request
     * @param array   $attributes
     */
    protected function addRequestTags(Request $request, array $attributes)
    {
        foreach ($attributes as $attribute) {
            $this->addRequestTag($request, $attribute);
        }
    }

    /**
     * @param array $parameter
     * @param array $keys
     */
    protected function addParameterTags(array $parameter, array $keys)
    {
        foreach ($keys as $key) {
            $this->addParameterTag($parameter, $key);
        }
    }

    /**
     * @return string
     */
    protected function getCacheKey(): string
    {
        return implode('|', $this->cacheTags);
    }

    /**
     * Set the cache lifetime
     *
     * @param \DateInterval $time
     */
    protected function setCacheLifetime(\DateInterval $time)
    {
        $this->cacheLifetime = $time;
    }

    /**
     * @param \DateTime $cacheExpiresAt
     */
    protected function setCacheExpiresAt(\DateTime $cacheExpiresAt)
    {
        $this->cacheExpiresAt = $cacheExpiresAt;
    }
}
