<?php

namespace ClickNow\Checker\Result;

use ClickNow\Checker\Action\ActionInterface;
use ClickNow\Checker\Command\CommandInterface;
use ClickNow\Checker\Context\ContextInterface;

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
     * @var \ClickNow\Checker\Command\CommandInterface
     */
    private $command;

    /**
     * @var \ClickNow\Checker\Context\ContextInterface
     */
    private $context;

    /**
     * @var \ClickNow\Checker\Action\ActionInterface
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
     * @param \ClickNow\Checker\Command\CommandInterface $command
     * @param \ClickNow\Checker\Context\ContextInterface $context
     * @param \ClickNow\Checker\Action\ActionInterface   $action
     * @param null|string                                $message
     */
    public function __construct(
        $status,
        CommandInterface $command,
        ContextInterface $context,
        ActionInterface $action,
        $message = null
    ) {
        $this->status = $status;
        $this->command = $command;
        $this->context = $context;
        $this->action = $action;
        $this->message = $message;
    }

    /**
     * Create result by status skipped.
     *
     * @param \ClickNow\Checker\Command\CommandInterface $command
     * @param \ClickNow\Checker\Context\ContextInterface $context
     * @param \ClickNow\Checker\Action\ActionInterface   $action
     *
     * @return \ClickNow\Checker\Result\ResultInterface
     */
    public static function skipped(
        CommandInterface $command,
        ContextInterface $context,
        ActionInterface $action
    ) {
        return new self(self::SKIPPED, $command, $context, $action);
    }

    /**
     * Create result by status success.
     *
     * @param \ClickNow\Checker\Command\CommandInterface $command
     * @param \ClickNow\Checker\Context\ContextInterface $context
     * @param \ClickNow\Checker\Action\ActionInterface   $action
     *
     * @return \ClickNow\Checker\Result\ResultInterface
     */
    public static function success(
        CommandInterface $command,
        ContextInterface $context,
        ActionInterface $action
    ) {
        return new self(self::SUCCESS, $command, $context, $action);
    }

    /**
     * Create result by status warning.
     *
     * @param \ClickNow\Checker\Command\CommandInterface $command
     * @param \ClickNow\Checker\Context\ContextInterface $context
     * @param \ClickNow\Checker\Action\ActionInterface   $action
     * @param string                                     $message
     *
     * @return \ClickNow\Checker\Result\ResultInterface
     */
    public static function warning(
        CommandInterface $command,
        ContextInterface $context,
        ActionInterface $action,
        $message
    ) {
        return new self(self::WARNING, $command, $context, $action, $message);
    }

    /**
     * Create result by status error.
     *
     * @param \ClickNow\Checker\Command\CommandInterface $command
     * @param \ClickNow\Checker\Context\ContextInterface $context
     * @param \ClickNow\Checker\Action\ActionInterface   $action
     * @param string                                     $message
     *
     * @return \ClickNow\Checker\Result\ResultInterface
     */
    public static function error(
        CommandInterface $command,
        ContextInterface $context,
        ActionInterface $action,
        $message
    ) {
        return new self(self::ERROR, $command, $context, $action, $message);
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
     * Get command.
     *
     * @return \ClickNow\Checker\Command\CommandInterface
     */
    public function getCommand()
    {
        return $this->command;
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
     * @return \ClickNow\Checker\Action\ActionInterface
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
