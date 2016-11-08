<?php

namespace ClickNow\Checker\Action;

use ClickNow\Checker\Command\CommandInterface;
use ClickNow\Checker\Context\ContextInterface;

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
     * @param \ClickNow\Checker\Command\CommandInterface $command
     * @param \ClickNow\Checker\Context\ContextInterface $context
     *
     * @return bool
     */
    public function canRunInContext(CommandInterface $command, ContextInterface $context);

    /**
     * Run.
     *
     * @param \ClickNow\Checker\Command\CommandInterface $command
     * @param \ClickNow\Checker\Context\ContextInterface $context
     *
     * @return \ClickNow\Checker\Result\ResultInterface
     */
    public function run(CommandInterface $command, ContextInterface $context);
}
