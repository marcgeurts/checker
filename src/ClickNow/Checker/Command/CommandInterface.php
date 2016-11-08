<?php

namespace ClickNow\Checker\Command;

use ClickNow\Checker\Action\ActionInterface;
use ClickNow\Checker\Context\ContextInterface;

interface CommandInterface extends ActionInterface
{
    /**
     * Get actions.
     *
     * @return \ClickNow\Checker\Action\ActionsCollection
     */
    public function getActions();

    /**
     * Add action.
     *
     * @param \ClickNow\Checker\Action\ActionInterface $action
     * @param array                                    $config
     *
     * @return void
     */
    public function addAction(ActionInterface $action, array $config);

    /**
     * Set config.
     *
     * @param array $config
     *
     * @return void
     */
    public function setConfig(array $config);

    /**
     * Get process timeout.
     *
     * @return null|float
     */
    public function getProcessTimeout();

    /**
     * Is stop on failure?
     *
     * @return bool
     */
    public function isStopOnFailure();

    /**
     * Is ignore unstaged changes?
     *
     * @return bool
     */
    public function isIgnoreUnstagedChanges();

    /**
     * Is skip success output?
     *
     * @return bool
     */
    public function isSkipSuccessOutput();

    /**
     * Get message.
     *
     * @param string $resource
     *
     * @return null|string
     */
    public function getMessage($resource);

    /**
     * Get action metadata.
     *
     * @param \ClickNow\Checker\Action\ActionInterface $action
     *
     * @return array
     */
    public function getActionMetadata(ActionInterface $action);

    /**
     * Get action priority.
     *
     * @param \ClickNow\Checker\Action\ActionInterface $action
     *
     * @return int
     */
    public function getActionPriority(ActionInterface $action);

    /**
     * Is action blocking?
     *
     * @param \ClickNow\Checker\Action\ActionInterface $action
     *
     * @return bool
     */
    public function isActionBlocking(ActionInterface $action);

    /**
     * Get action config.
     *
     * @param \ClickNow\Checker\Action\ActionInterface $action
     *
     * @return array
     */
    public function getActionConfig(ActionInterface $action);

    /**
     * Get actions to run.
     *
     * @param \ClickNow\Checker\Context\ContextInterface $context
     *
     * @return \ClickNow\Checker\Action\ActionsCollection
     */
    public function getActionsToRun(ContextInterface $context);

    /**
     * Run action.
     *
     * @param \ClickNow\Checker\Context\ContextInterface $context
     * @param \ClickNow\Checker\Action\ActionInterface   $action
     *
     * @return \ClickNow\Checker\Result\ResultInterface
     */
    public function runAction(ContextInterface $context, ActionInterface $action);
}
