<?php

namespace ClickNow\Checker\Runner;

use ClickNow\Checker\Context\ContextInterface;

interface RunnerInterface extends ActionInterface
{
    /**
     * Get process timeout.
     *
     * @return float
     */
    public function getProcessTimeout();

    /**
     * Set process timeout.
     *
     * @param float $processTimeout
     *
     * @return void
     */
    public function setProcessTimeout($processTimeout);

    /**
     * Get process async wait.
     *
     * @return int
     */
    public function getProcessAsyncWait();

    /**
     * Set process async wait.
     *
     * @param int $processAsyncWait
     *
     * @return void
     */
    public function setProcessAsyncWait($processAsyncWait);

    /**
     * Get process async limit.
     *
     * @return int
     */
    public function getProcessAsyncLimit();

    /**
     * Set process async limit.
     *
     * @param int $processAsyncLimit
     *
     * @return void
     */
    public function setProcessAsyncLimit($processAsyncLimit);

    /**
     * Is stop on failure?
     *
     * @return bool
     */
    public function isStopOnFailure();

    /**
     * Set stop on failure.
     *
     * @param bool $stopOnFailure
     *
     * @return void
     */
    public function setStopOnFailure($stopOnFailure);

    /**
     * Is ignore unstaged changes?
     *
     * @return bool
     */
    public function isIgnoreUnstagedChanges();

    /**
     * Set ignore unstaged changes.
     *
     * @param bool $ignoreUnstagedChanges
     *
     * @return void
     */
    public function setIgnoreUnstagedChanges($ignoreUnstagedChanges);

    /**
     * Is strict?
     *
     * @return bool
     */
    public function isStrict();

    /**
     * Set strict.
     *
     * @param bool $strict
     *
     * @return void
     */
    public function setStrict($strict);

    /**
     * Is skip success output?
     *
     * @return bool
     */
    public function isSkipSuccessOutput();

    /**
     * Set skip success output.
     *
     * @param bool $skipSuccessOutput
     *
     * @return void
     */
    public function setSkipSuccessOutput($skipSuccessOutput);

    /**
     * Get progress.
     *
     * @return string
     */
    public function getProgress();

    /**
     * Set progress.
     *
     * @param string $progress
     *
     * @return void
     */
    public function setProgress($progress);

    /**
     * Get message.
     *
     * @param string $resource
     *
     * @return null|string
     */
    public function getMessage($resource);

    /**
     * Set message.
     *
     * @param array $message
     *
     * @return void
     */
    public function setMessage(array $message);

    /**
     * Get actions.
     *
     * @return \ClickNow\Checker\Runner\ActionsCollection
     */
    public function getActions();

    /**
     * Add action.
     *
     * @param \ClickNow\Checker\Runner\ActionInterface $action
     * @param array                                    $config
     *
     * @return void
     */
    public function addAction(ActionInterface $action, array $config);

    /**
     * Get action metadata.
     *
     * @param \ClickNow\Checker\Runner\ActionInterface $action
     *
     * @return array
     */
    public function getActionMetadata(ActionInterface $action);

    /**
     * Get action priority.
     *
     * @param \ClickNow\Checker\Runner\ActionInterface $action
     *
     * @return int
     */
    public function getActionPriority(ActionInterface $action);

    /**
     * Is action blocking?
     *
     * @param \ClickNow\Checker\Runner\ActionInterface $action
     *
     * @return bool
     */
    public function isActionBlocking(ActionInterface $action);

    /**
     * Get action config.
     *
     * @param \ClickNow\Checker\Runner\ActionInterface $action
     *
     * @return array
     */
    public function getActionConfig(ActionInterface $action);

    /**
     * Get actions to run.
     *
     * @param \ClickNow\Checker\Context\ContextInterface $context
     *
     * @return \ClickNow\Checker\Runner\ActionsCollection
     */
    public function getActionsToRun(ContextInterface $context);

    /**
     * Run action.
     *
     * @param \ClickNow\Checker\Context\ContextInterface $context
     * @param \ClickNow\Checker\Runner\ActionInterface   $action
     *
     * @return \ClickNow\Checker\Result\ResultInterface
     */
    public function runAction(ContextInterface $context, ActionInterface $action);
}
