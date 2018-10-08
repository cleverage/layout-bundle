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

use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

/**
 * Represents an error due to a wrong configuration of the layout
 */
class UnsortableLayoutException extends InvalidConfigurationException
{
    /**
     * @param string $slotCode
     * @param array  $currentChildren
     * @param array  $unsortedChildren
     *
     * @return UnsortableLayoutException
     */
    public static function create(
        string $slotCode,
        array $currentChildren,
        array $unsortedChildren
    ): UnsortableLayoutException {
        $currentCount = \count($currentChildren);
        $unsortableCount = \count($unsortedChildren);

        return new self(
            "Unable to sort {$unsortableCount}/{$currentCount} children inside slot {$slotCode}"
        );
    }
}
