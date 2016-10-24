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
     * Get command.
     *
     * @return \ClickNow\Checker\Command\CommandInterface
     */
    public function getCommand();

    /**
     * Get context.
     *
     * @return \ClickNow\Checker\Context\ContextInterface
     */
    public function getContext();

    /**
     * Get action.
     *
     * @return \ClickNow\Checker\Action\ActionInterface
     */
    public function getAction();

    /**
     * Get message.
     *
     * @return string|null
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
