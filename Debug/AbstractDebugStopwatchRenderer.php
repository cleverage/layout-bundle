<?php

namespace CleverAge\LayoutBundle\Debug;

use Symfony\Component\Stopwatch\Stopwatch;

/**
 * Common logic to all debug renderers
 */
abstract class AbstractDebugStopwatchRenderer
{
    /** @var Stopwatch|null */
    protected $stopwatch;

    /**
     * @param string   $type
     * @param string   $code
     * @param callable $callback
     *
     * @return string
     */
    protected function wrapHtml(string $type, string $code, callable $callback): string
    {
        if ($this->stopwatch) {
            $this->stopwatch->start("render.{$type}.{$code}");
        }

        $html = $callback();

        if ($this->stopwatch) {
            $this->stopwatch->stop("render.{$type}.{$code}");
        }

        return $html;
    }
}
