<?php

namespace CleverAge\LayoutBundle\Exception;


use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

/**
 * Represents an error due to a wrong configuration of the layout
 */
class UnsortableLayoutException extends InvalidConfigurationException
{
    /**
     * @param string $slotCode
     * @param array $currentChildren
     * @param array $unsortedChildren
     * @return UnsortableLayoutException
     */
    public static function create(
        string $slotCode,
        array $currentChildren,
        array $unsortedChildren
    ): UnsortableLayoutException {
        $currentCount = count($currentChildren);
        $unsortableCount = count($unsortedChildren);

        return new UnsortableLayoutException(
            "Unable to sort {$unsortableCount}/{$currentCount} children inside slot {$slotCode}"
        );
    }
}
