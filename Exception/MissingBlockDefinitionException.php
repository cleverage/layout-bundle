<?php
/*
 * This file is part of the CleverAge/LayoutBundle package.
 *
 * Copyright (c) 2015-2018 Clever-Age
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CleverAge\LayoutBundle\Exception;

/**
 * Thrown when trying to access a missing block definition
 */
class MissingBlockDefinitionException extends MissingException
{
    /**
     * @param string $blockDefinitionCode
     *
     * @return MissingBlockDefinitionException
     */
    public static function create(string $blockDefinitionCode) : MissingBlockDefinitionException
    {
        return new MissingBlockDefinitionException("Missing block definition '{$blockDefinitionCode}'");
    }
}
