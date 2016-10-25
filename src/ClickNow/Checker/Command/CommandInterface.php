<?php

namespace ClickNow\Checker\Command;

use ClickNow\Checker\Action\ActionInterface;
use ClickNow\Checker\Context\ContextInterface;

interface CommandInterface extends ActionInterface
{
    /**
     * Get actions of this command.
     *
     * @return \ClickNow\Checker\Action\ActionsCollection
     */
    public function getActions();

    /**
     * Add action in this command.
     *
     * @param \ClickNow\Checker\Action\ActionInterface $action
     * @param array                                    $config
     *
     * @return void
     */
    public function addAction(ActionInterface $action, array $config);

    /**
     * Set config for this command.
     *
     * @param array $config
     *
     * @return void
     */
    public function setConfig(array $config);

    /**
     * Get process timeout for this command.
     *
     * @return null|float
     */
    public function getProcessTimeout();

    /**
     * Should stop running command on failure?
     *
     * @return bool
     */
    public function shouldStopOnFailure();

    /**
     * Should ignore unstaged changes for this command?
     *
     * @return bool
     */
    public function shouldIgnoreUnstagedChanges();

    /**
     * It is to skip the success output for this command?
     *
     * @return bool
     */
    public function isSkipSuccessOutput();

    /**
     * Get message by resource for this command.
     *
     * @param string $resource
     *
     * @return null|string
     */
    public function getMessage($resource);

    /**
     * Get metadata by action for this command.
     *
     * @param \ClickNow\Checker\Action\ActionInterface $action
     *
     * @return array
     */
    public function getActionMetadata(ActionInterface $action);

    /**
     * Get priority by action for this command.
     *
     * @param \ClickNow\Checker\Action\ActionInterface $action
     *
     * @return int
     */
    public function getPriorityAction(ActionInterface $action);

    /**
     * Is blocking action for this command?
     *
     * @param \ClickNow\Checker\Action\ActionInterface $action
     *
     * @return bool
     */
    public function isBlockingAction(ActionInterface $action);

    /**
     * Get config by action for this command.
     *
     * @param \ClickNow\Checker\Action\ActionInterface $action
     *
     * @return array
     */
    public function getActionConfig(ActionInterface $action);

    /**
     * Get actions to run for this command.
     *
     * @param \ClickNow\Checker\Context\ContextInterface $context
     *
     * @return \ClickNow\Checker\Action\ActionsCollection
     */
    public function getActionsToRun(ContextInterface $context);

    /**
     * Run by action for this command.
     *
     * @param \ClickNow\Checker\Context\ContextInterface $context
     * @param \ClickNow\Checker\Action\ActionInterface   $action
     *
     * @return \ClickNow\Checker\Result\ResultInterface
     */
    public function runAction(ContextInterface $context, ActionInterface $action);
}
