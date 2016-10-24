<?php

namespace ClickNow\Checker\Action;

use ClickNow\Checker\Command\CommandInterface;
use ClickNow\Checker\Context\ContextInterface;

interface ActionInterface
{
    /**
     * Get action name.
     *
     * @return string
     */
    public function getName();

    /**
     * This action can run in context?
     *
     * @param \ClickNow\Checker\Command\CommandInterface $command
     * @param \ClickNow\Checker\Context\ContextInterface $context
     *
     * @return bool
     */
    public function canRunInContext(CommandInterface $command, ContextInterface $context);

    /**
     * Run this action.
     *
     * @param \ClickNow\Checker\Command\CommandInterface $command
     * @param \ClickNow\Checker\Context\ContextInterface $context
     *
     * @return \ClickNow\Checker\Result\ResultInterface
     */
    public function run(CommandInterface $command, ContextInterface $context);
}
