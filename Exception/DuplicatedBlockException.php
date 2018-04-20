<?php

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
