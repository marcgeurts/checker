<?php

namespace ClickNow\Checker\Action;

use ClickNow\Checker\Context\ContextInterface;
use ClickNow\Checker\Runner\RunnerInterface;

interface ActionInterface
{
    /**
     * Get name.
     *
     * @return string
     */
    public function getName();

    /**
     * Can run in context?
     *
     * @param \ClickNow\Checker\Runner\RunnerInterface   $runner
     * @param \ClickNow\Checker\Context\ContextInterface $context
     *
     * @return bool
     */
    public function canRunInContext(RunnerInterface $runner, ContextInterface $context);

    /**
     * Run.
     *
     * @param \ClickNow\Checker\Runner\RunnerInterface   $runner
     * @param \ClickNow\Checker\Context\ContextInterface $context
     *
     * @return \ClickNow\Checker\Result\ResultInterface
     */
    public function run(RunnerInterface $runner, ContextInterface $context);
}
