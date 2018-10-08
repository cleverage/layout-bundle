<?php

namespace CleverAge\LayoutBundle\Layout;

use CleverAge\LayoutBundle\Block\BlockInterface;
use CleverAge\LayoutBundle\Block\BlockRegistry;
use CleverAge\LayoutBundle\Exception\MissingSlotException;
use CleverAge\LayoutBundle\Exception\MissingBlockException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Stopwatch\Stopwatch;

/**
 * Represent a layout, hold the main (inherited) parameters and manage slot rendering
 */
class Layout implements LayoutInterface
{
    /** @var BlockRegistry */
    protected $blockRegistry;

    /** @var string */
    protected $code;

    /** @var string */
    protected $template;

    /** @var array */
    protected $globalParameters;

    /** @var Slot[] */
    protected $slots;

    /** @var string */
    protected $debugMode;

    /** @var  BlockInterface[] */
    protected $blocks;

    /** @var string[][] */
    protected $blockHtml = [];

    /** @var Stopwatch */
    protected $stopwatch;

    /**
     * @param BlockRegistry $blockRegistry
     * @param string        $code
     * @param string        $template
     * @param Slot[]        $slots
     * @param array         $globalParameters
     */
    public function __construct(
        BlockRegistry $blockRegistry,
        string $code,
        string $template,
        array $slots,
        array $globalParameters = []
    ) {
        $this->blockRegistry = $blockRegistry;
        $this->code = $code;
        $this->template = $template;
        $this->slots = $slots;
        $this->globalParameters = $globalParameters;
    }

    /**
     * @return string
     */
    public function getCode(): string
    {
        return $this->code;
    }

    /**
     * @return string
     */
    public function getTemplate(): string
    {
        return $this->template;
    }

    /**
     * @return array
     */
    public function getGlobalParameters(): array
    {
        return $this->globalParameters;
    }

    /**
     * @return Slot[]
     */
    public function getSlots(): array
    {
        return $this->slots;
    }

    /**
     * @param string $slotCode
     *
     * @throws MissingSlotException
     *
     * @return Slot
     */
    public function getSlot(string $slotCode): Slot
    {
        if (!array_key_exists($slotCode, $this->slots)) {
            throw MissingSlotException::create($slotCode);
        }

        return $this->slots[$slotCode];
    }

    /**
     * @param string $code
     *
     * @throws MissingBlockException
     *
     * @return BlockInterface
     */
    public function getBlock(string $code): BlockInterface
    {
        return $this->blockRegistry->getBlock($code);
    }

    /**
     * @param string $slotCode
     *
     * @return array
     */
    public function getBlocksHtml(string $slotCode): array
    {
        if (!isset($this->blockHtml[$slotCode])) {
            $slot = $this->getSlot($slotCode);
            $this->blockHtml[$slotCode] = [];

            if ($this->stopwatch) {
                $this->stopwatch->start('layout.render');
            }

            foreach ($slot->getBlockDefinitions() as $blockDefinition) {
                if ($blockDefinition->isDisplayed()) {
                    /** @var BlockInterface $block */
                    $block = $this->blockRegistry->getBlock($blockDefinition->getBlockCode());
                    $parameters = array_merge(
                        [
                            '_layout' => $this,
                            '_slot' => $slot,
                            '_block_definition' => $blockDefinition,
                        ],
                        $this->globalParameters,
                        $blockDefinition->getParameters()
                    );
                    try {
                        if ($this->stopwatch) {
                            $this->stopwatch->start('layout.render.'.$block->getCode());
                        }

                        $this->blockHtml[$slotCode][$blockDefinition->getCode()] = $block->render($parameters);

                        if ($this->stopwatch) {
                            $this->stopwatch->stop('layout.render.'.$block->getCode());
                        }
                    } catch (\Throwable $exception) {
                        $originalBlockCode = $blockDefinition->getBlockCode();
                        throw new \RuntimeException(
                            "Error while rendering block {$originalBlockCode} in layout {$this->getCode()}",
                            0,
                            $exception
                        );
                    }
                }
            }

            if ($this->stopwatch) {
                $this->stopwatch->stop('layout.render');
            }
        }

        return $this->blockHtml[$slotCode];
    }

    /**
     * TODO : refactor with previous method, but there is some issues when there is more than one blockDef with the
     *        same block code
     * TODO : prevent double init
     *
     *
     *
     * {@inheritdoc}
     */
    public function initializeBlocks(Request $request): void
    {
        if ($this->stopwatch) {
            $this->stopwatch->start('layout.initialization');
        }

        foreach ($this->getSlots() as $slot) {
            foreach ($slot->getBlockDefinitions() as $blockDefinition) {
                if ($blockDefinition->isDisplayed()) {
                    /** @var BlockInterface $block */
                    $block = $this->blockRegistry->getBlock($blockDefinition->getBlockCode());

                    $parameters = array_merge($this->globalParameters, $blockDefinition->getParameters());
                    try {
                        if ($this->stopwatch) {
                            $this->stopwatch->start('layout.initialization.'.$block->getCode());
                        }

                        $block->initialize($request, $parameters);

                        if ($this->stopwatch) {
                            $this->stopwatch->stop('layout.initialization.'.$block->getCode());
                        }
                    } catch (\Throwable $exception) {
                        $originalBlockCode = $blockDefinition->getBlockCode();
                        throw new \RuntimeException(
                            "Error while initializing block {$originalBlockCode} in layout {$this->getCode()}",
                            0,
                            $exception
                        );
                    }
                }
            }
        }

        if ($this->stopwatch) {
            $this->stopwatch->stop('layout.initialization');
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getSlotBlockCount(array $slotCodes): int
    {
        $count = 0;
        foreach ($slotCodes as $slotCode) {
            foreach ($this->getBlocksHtml($slotCode) as $blockHtml) {
                if (!empty(trim($blockHtml))) {
                    ++$count;
                }
            }
        }

        return $count;
    }


    /**
     * @return string
     */
    public function getDebugMode(): string
    {
        return $this->debugMode ?: '';
    }

    /**
     * @param string $debugMode
     */
    public function setDebugMode(string $debugMode): void
    {
        $this->debugMode = $debugMode;
    }

    /**
     * @param Stopwatch $stopwatch
     */
    public function setStopwatch(Stopwatch $stopwatch): void
    {
        $this->stopwatch = $stopwatch;
    }
}
