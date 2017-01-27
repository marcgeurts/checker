<?php

namespace ClickNow\Checker\Result;

use ClickNow\Checker\Context\ContextInterface;
use ClickNow\Checker\Runner\ActionInterface;
use ClickNow\Checker\Runner\RunnerInterface;

class Result implements ResultInterface
{
    const SKIPPED = -1;
    const SUCCESS = 0;
    const WARNING = 1;
    const ERROR = 2;

    /**
     * @var int
     */
    private $status;

    /**
     * @var \ClickNow\Checker\Runner\RunnerInterface
     */
    private $runner;

    /**
     * @var \ClickNow\Checker\Context\ContextInterface
     */
    private $context;

    /**
     * @var \ClickNow\Checker\Runner\ActionInterface
     */
    private $action;

    /**
     * @var null|string
     */
    private $message;

    /**
     * Result.
     *
     * @param int                                        $status
     * @param \ClickNow\Checker\Runner\RunnerInterface   $runner
     * @param \ClickNow\Checker\Context\ContextInterface $context
     * @param \ClickNow\Checker\Runner\ActionInterface   $action
     * @param null|string                                $message
     */
    public function __construct(
        $status,
        RunnerInterface $runner,
        ContextInterface $context,
        ActionInterface $action,
        $message = null
    ) {
        $this->status = $status;
        $this->runner = $runner;
        $this->context = $context;
        $this->action = $action;
        $this->message = $message;
    }

    /**
     * Skipped.
     *
     * @param \ClickNow\Checker\Runner\RunnerInterface   $runner
     * @param \ClickNow\Checker\Context\ContextInterface $context
     * @param \ClickNow\Checker\Runner\ActionInterface   $action
     *
     * @return \ClickNow\Checker\Result\ResultInterface
     */
    public static function skipped(
        RunnerInterface $runner,
        ContextInterface $context,
        ActionInterface $action
    ) {
        return new self(self::SKIPPED, $runner, $context, $action);
    }

    /**
     * Success.
     *
     * @param \ClickNow\Checker\Runner\RunnerInterface   $runner
     * @param \ClickNow\Checker\Context\ContextInterface $context
     * @param \ClickNow\Checker\Runner\ActionInterface   $action
     *
     * @return \ClickNow\Checker\Result\ResultInterface
     */
    public static function success(
        RunnerInterface $runner,
        ContextInterface $context,
        ActionInterface $action
    ) {
        return new self(self::SUCCESS, $runner, $context, $action);
    }

    /**
     * Warning.
     *
     * @param \ClickNow\Checker\Runner\RunnerInterface   $runner
     * @param \ClickNow\Checker\Context\ContextInterface $context
     * @param \ClickNow\Checker\Runner\ActionInterface   $action
     * @param string                                     $message
     *
     * @return \ClickNow\Checker\Result\ResultInterface
     */
    public static function warning(
        RunnerInterface $runner,
        ContextInterface $context,
        ActionInterface $action,
        $message
    ) {
        return new self(self::WARNING, $runner, $context, $action, $message);
    }

    /**
     * Error.
     *
     * @param \ClickNow\Checker\Runner\RunnerInterface   $runner
     * @param \ClickNow\Checker\Context\ContextInterface $context
     * @param \ClickNow\Checker\Runner\ActionInterface   $action
     * @param string                                     $message
     *
     * @return \ClickNow\Checker\Result\ResultInterface
     */
    public static function error(
        RunnerInterface $runner,
        ContextInterface $context,
        ActionInterface $action,
        $message
    ) {
        return new self(self::ERROR, $runner, $context, $action, $message);
    }

    /**
     * Get status.
     *
     * @return int
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Get runner.
     *
     * @return \ClickNow\Checker\Runner\RunnerInterface
     */
    public function getRunner()
    {
        return $this->runner;
    }

    /**
     * Get context.
     *
     * @return \ClickNow\Checker\Context\ContextInterface
     */
    public function getContext()
    {
        return $this->context;
    }

    /**
     * Get action.
     *
     * @return \ClickNow\Checker\Runner\ActionInterface
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * Get message.
     *
     * @return null|string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * Is skipped?
     *
     * @return bool
     */
    public function isSkipped()
    {
        return $this->getStatus() === self::SKIPPED;
    }

    /**
     * Is success?
     *
     * @return bool
     */
    public function isSuccess()
    {
        return $this->getStatus() === self::SUCCESS;
    }

    /**
     * Is warning?
     *
     * @return bool
     */
    public function isWarning()
    {
        return $this->getStatus() === self::WARNING;
    }

    /**
     * Is error?
     *
     * @return bool
     */
    public function isError()
    {
        return $this->getStatus() === self::ERROR;
    }
}
