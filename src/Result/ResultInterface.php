<?php

namespace ClickNow\Checker\Result;

interface ResultInterface
{
    /**
     * Get status.
     *
     * @return int
     */
    public function getStatus();

    /**
     * Get runner.
     *
     * @return \ClickNow\Checker\Runner\RunnerInterface
     */
    public function getRunner();

    /**
     * Get context.
     *
     * @return \ClickNow\Checker\Context\ContextInterface
     */
    public function getContext();

    /**
     * Get action.
     *
     * @return \ClickNow\Checker\Runner\ActionInterface
     */
    public function getAction();

    /**
     * Get message.
     *
     * @return null|string
     */
    public function getMessage();

    /**
     * Is skipped?
     *
     * @return bool
     */
    public function isSkipped();

    /**
     * Is success?
     *
     * @return bool
     */
    public function isSuccess();

    /**
     * Is warning?
     *
     * @return bool
     */
    public function isWarning();

    /**
     * Is error?
     *
     * @return bool
     */
    public function isError();
}
