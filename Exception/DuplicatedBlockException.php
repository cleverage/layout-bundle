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
 * Error thrown when 2 blocks use the same code
 */
class DuplicatedBlockException extends \InvalidArgumentException
{
    /**
     * @param string $blockCode
     * @return DuplicatedBlockException
     */
    public static function create(string $blockCode): DuplicatedBlockException
    {
        return new DuplicatedBlockException("The code {$blockCode} is already used");
    }
}
