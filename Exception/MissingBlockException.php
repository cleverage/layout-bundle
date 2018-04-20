<?php

namespace CleverAge\LayoutBundle\Exception;

/**
 * Thrown when trying to access a missing block
 */
class MissingBlockException extends MissingException
{
    /**
     * @param string $blockCode
     *
     * @return MissingBlockException
     */
    public static function create(string $blockCode) : MissingBlockException
    {
        return new MissingBlockException("Missing block '{$blockCode}'");
    }
}
