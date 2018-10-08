<?php
/*
 * This file is part of the CleverAge/LayoutBundle package.
 *
 * Copyright (c) 2015-2018 Clever-Age
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CleverAge\LayoutBundle\Annotation;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\ConfigurationAnnotation;

/**
 * @Annotation
 */
class Layout extends ConfigurationAnnotation
{
    /** @var string|null */
    protected $name;

    /**
     * @param array $values
     */
    public function __construct(array $values)
    {
        $this->name = $values['value'] ?? null;
        unset($values['value']);
        parent::__construct($values);
    }

    /**
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string|null $name
     */
    public function setName(string $name = null): void
    {
        $this->name = $name;
    }

    /**
     * {@inheritdoc}
     */
    public function getAliasName(): string
    {
        return 'layout';
    }

    /**
     * {@inheritdoc}
     */
    public function allowArray(): bool
    {
        return false;
    }
}
