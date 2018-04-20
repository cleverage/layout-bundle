<?php

namespace CleverAge\LayoutBundle\Layout;

use CleverAge\LayoutBundle\Exception\MissingBlockDefinitionException;
use CleverAge\LayoutBundle\Exception\UnsortableLayoutException;

/**
 * Hold and sort block definitions
 */
class Slot
{
    /** @var string */
    protected $code;

    /** @var BlockDefinition[] */
    protected $blockDefinitions = [];

    /** @var BlockDefinition[] */
    protected $sortedBlockDefinitions;

    /**
     * Slot constructor.
     * @param string $code
     */
    public function __construct($code)
    {
        $this->code = $code;
    }

    /**
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }


    /**
     * Add a block to a slot and invalid cached sort
     * @param BlockDefinition $blockDefinition
     */
    public function addBlockDefinition(BlockDefinition $blockDefinition)
    {
        $this->blockDefinitions[$blockDefinition->getCode()] = $blockDefinition;
        $this->sortedBlockDefinitions = null;
    }

    /**
     * Sort and get the list of children blocks
     * @return BlockDefinition[]
     */
    public function getBlockDefinitions(): array
    {
        if (!isset($this->sortedBlockDefinitions)) {
            $this->sortedBlockDefinitions = $this->sortBlockDefinitions($this->blockDefinitions);
        }

        return $this->sortedBlockDefinitions;
    }

    /**
     * @param string $blockDefinitionCode
     *
     * @throws MissingBlockDefinitionException
     *
     * @return BlockDefinition
     */
    public function getBlockDefinition(string $blockDefinitionCode): BlockDefinition
    {
        if (!$this->hasBlockDefinition($blockDefinitionCode)) {
            throw MissingBlockDefinitionException::create($blockDefinitionCode);
        }

        return $this->blockDefinitions[$blockDefinitionCode];
    }

    /**
     * @param string $blockDefinitionCode
     *
     * @return bool
     */
    public function hasBlockDefinition(string $blockDefinitionCode): bool
    {
        return array_key_exists($blockDefinitionCode, $this->blockDefinitions);
    }

    /**
     * Sort the blocks according to the after/before positioning
     * @param BlockDefinition[] $blockDefinitions
     *
     * @throws \Exception
     *
     * @return BlockDefinition[]
     */
    protected function sortBlockDefinitions(array $blockDefinitions): array
    {
        /**
         * @var BlockDefinition[] $orderedChildren
         * @var BlockDefinition[] $childrenToSort
         */
        $orderedChildren = [];
        $childrenToSort = [];

        // Separate unordered items and items to sort
        foreach ($blockDefinitions as $definition) {
            if ($definition->getAfter() || $definition->getBefore()) {
                $childrenToSort[$definition->getCode()] = $definition;
            } else {
                $orderedChildren[$definition->getCode()] = $definition;
            }
        }

        // Due to unknown initial order, we must try to sort until success or stalled progress
        while (count($childrenToSort) > 0) {
            /** @var BlockDefinition[] $sortedChildren */
            $sortedChildren = [];

            // Try to sort every child one by one
            foreach ($childrenToSort as $definition) {
                if ($definition->getAfter() === '*') {
                    // Absolute after sorting
                    $orderedChildren[$definition->getCode()] = $definition;
                    $sortedChildren[$definition->getCode()] = $definition;
                } elseif ($definition->getBefore() === '*') {
                    // Absolute before sorting
                    $orderedChildren = [$definition->getCode() => $definition] + $orderedChildren;
                    $sortedChildren[$definition->getCode()] = $definition;
                } elseif ($definition->getAfter() || $definition->getBefore()) {
                    // Relative sorting
                    $relativeDefinitionCode = !empty($definition->getAfter()) ? $definition->getAfter(
                    ) : $definition->getBefore();
                    $position = array_search($relativeDefinitionCode, array_keys($orderedChildren), true);
                    if ($position !== false) {
                        /** @noinspection NotOptimalIfConditionsInspection */
                        if ($definition->getAfter()) {
                            $position++;
                        }

                        $before = array_slice($orderedChildren, 0, $position);
                        $after = array_slice($orderedChildren, $position);
                        $orderedChildren = $before + [$definition->getCode() => $definition] + $after;
                        $sortedChildren[$definition->getCode()] = $definition;
                    }
                }
            }

            // If the progress has stalled, throw an error
            if (!count($sortedChildren)) {
                throw UnsortableLayoutException::create($this->code, $blockDefinitions, $childrenToSort);
            }

            // Remove sorted children
            foreach ($sortedChildren as $code => $child) {
                unset($childrenToSort[$code]);
            }
        }

        return $orderedChildren;
    }
}