<?php

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
