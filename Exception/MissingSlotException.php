<?php

namespace CleverAge\LayoutBundle\Exception;

/**
 * Thrown when trying to access a missing slot
 */
class MissingSlotException extends MissingException
{
    /**
     * @param string $slotCode
     *
     * @return MissingSlotException
     */
    public static function create(string $slotCode) : MissingSlotException
    {
        return new MissingSlotException("Missing slot '{$slotCode}'");
    }
}
