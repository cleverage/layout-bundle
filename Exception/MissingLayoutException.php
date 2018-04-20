<?php

namespace CleverAge\LayoutBundle\Exception;

/**
 * Thrown when trying to access a missing layout
 */
class MissingLayoutException extends MissingException
{
    /**
     * @param string $layoutCode
     *
     * @return MissingLayoutException
     */
    public static function create(string $layoutCode) : MissingLayoutException
    {
        return new MissingLayoutException("Missing layout '{$layoutCode}'");
    }
}
