<?php

namespace CleverAge\LayoutBundle\Debug;

use Symfony\Component\Stopwatch\Stopwatch;
use Symfony\Component\Templating\EngineInterface;

/**
 * Common logic to all debug renderers
 */
abstract class AbstractDebugRenderer
{
    /** @var EngineInterface */
    protected $engine;

    /** @var Stopwatch|null */
    protected $stopwatch;

    /** @var bool */
    protected $debugMode = false;

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

        if ($this->debugMode) {
            return $this->engine->render(
                'CleverAgeLayoutBundle::debug.html.twig',
                [
                    'content' => $html,
                    'type' => $type,
                    'code' => $code,
                    'debug_mode' => 1,
                ]
            );
        }

        return $html;
    }
}
