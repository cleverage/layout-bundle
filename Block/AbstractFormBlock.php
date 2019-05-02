<?php
/*
 * This file is part of the CleverAge/LayoutBundle package.
 *
 * Copyright (c) 2015-2018 Clever-Age
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CleverAge\LayoutBundle\Block;

use function array_merge;
use CleverAge\LayoutBundle\Event\BlockInitializationEvent;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;

/**
 * Defines a standard block containing a form
 */
abstract class AbstractFormBlock extends SimpleBlock
{
    /** @var FormFactoryInterface */
    protected $formFactory;

    /** @var bool */
    protected $displayed = true;

    /** @var FormInterface */
    protected $form;

    /**
     * @param string               $code
     * @param string               $template
     * @param FormFactoryInterface $formFactory
     */
    public function __construct(
        string $code,
        string $template,
        FormFactoryInterface $formFactory
    ) {
        parent::__construct($code, $template);
        $this->formFactory = $formFactory;
    }

    /**
     * {@inheritDoc}
     */
    public function initialize(BlockInitializationEvent $event): void
    {
        parent::initialize($event);
        $form = $this->createForm($event);
        if (!$form) {
            $this->displayed = false;

            return;
        }

        $form->handleRequest($event->getRequest());
        if ($form->isSubmitted() && $form->isValid()) {
            $this->onFormSuccess($event, $form);
        }

        $this->form = $form;
    }

    /**
     * @param array $parameters
     *
     * @return string
     */
    public function render(array $parameters = []): string
    {
        if (!$this->displayed) {
            return '';
        }
        if ($this->form) {
            $parameters = array_merge($parameters, ['form' => $this->form->createView()]);
        }

        return parent::render($parameters);
    }

    /**
     * @param BlockInitializationEvent $event
     *
     * @return FormInterface|null
     */
    abstract protected function createForm(BlockInitializationEvent $event): ?FormInterface;

    /**
     * @param BlockInitializationEvent $event
     * @param FormInterface            $form
     */
    abstract protected function onFormSuccess(BlockInitializationEvent $event, FormInterface $form): void;
}
