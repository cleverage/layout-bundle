<?php
/*
 * This file is part of the CleverAge/LayoutBundle package.
 *
 * Copyright (c) 2015-2018 Clever-Age
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CleverAge\LayoutBundle\Layout;

use Symfony\Component\PropertyAccess\PropertyAccess;

/**
 * Represents the positioning of a block inside a layout's slot
 */
class BlockDefinition
{
    /** @var string */
    protected $code;

    /** @var string */
    protected $blockCode;

    /** @var array */
    protected $parameters = [];

    /** @var bool */
    protected $displayed = true;

    /** @var string */
    protected $after;

    /** @var string */
    protected $before;

    /**
     * @param string $code
     * @param array  $definition
     *
     * @throws \Symfony\Component\PropertyAccess\Exception\ExceptionInterface
     */
    public function __construct(string $code, array $definition = [])
    {
        $this->code = $code;
        $propertyAccessor = PropertyAccess::createPropertyAccessor();
        foreach ($definition as $key => $value) {
            $propertyAccessor->setValue($this, $key, $value);
        }
    }

    /**
     * This is the block's code inside a slot, uniquely representing it
     *
     * @return string
     */
    public function getCode(): string
    {
        return $this->code;
    }

    /**
     * This is the code of the service block, it can default to the code of the block himself
     *
     * @return string
     */
    public function getBlockCode(): string
    {
        if (null === $this->blockCode) {
            return $this->code;
        }

        return $this->blockCode;
    }

    /**
     * @return array
     */
    public function getParameters(): array
    {
        return $this->parameters;
    }

    /**
     * @param array $parameters
     * @param bool  $merge
     *
     * @return BlockDefinition
     */
    public function setParameters(array $parameters, $merge = false): BlockDefinition
    {
        if ($merge) {
            $this->parameters = array_merge($this->parameters, $parameters);
        } else {
            $this->parameters = $parameters;
        }

        return $this;
    }

    /**
     *
     * @param string $key
     * @param mixed  $value
     *
     * @return BlockDefinition
     */
    public function addParameter(string $key, $value): BlockDefinition
    {
        $this->parameters[$key] = $value;

        return $this;
    }

    /**
     * @return boolean
     */
    public function isDisplayed(): bool
    {
        return ('true' === $this->displayed || true === $this->displayed);
    }

    /**
     * @return string
     */
    public function getAfter(): string
    {
        return $this->after ?: '';
    }

    /**
     * @return string
     */
    public function getBefore(): string
    {
        return $this->before ?: '';
    }

    /**
     * @param string $blockCode
     */
    public function setBlockCode($blockCode): void
    {
        $this->blockCode = $blockCode;
    }

    /**
     * @param bool $displayed
     */
    public function setDisplayed($displayed): void
    {
        $this->displayed = $displayed;
    }

    /**
     * @param string $after
     */
    public function setAfter($after): void
    {
        $this->after = $after;
    }

    /**
     * @param string $before
     */
    public function setBefore($before): void
    {
        $this->before = $before;
    }
}
