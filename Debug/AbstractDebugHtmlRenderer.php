<?php

namespace CleverAge\LayoutBundle\Debug;

use Symfony\Component\Templating\EngineInterface;

/**
 * Common logic to all debug html renderers
 */
abstract class AbstractDebugHtmlRenderer
{
    /** @var EngineInterface */
    protected $engine;

    /**
     * @param string $type
     * @param string $code
     * @param string $html
     *
     * @return string
     */
    protected function wrapHtml(string $type, string $code, string $html): string
    {
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
}
