<?php

namespace ClickNow\Checker\Runner;

use ClickNow\Checker\Config\Checker;
use ClickNow\Checker\Context\ContextInterface;

trait ConfigRunner
{
    /**
     * @var array
     */
    public static $configs = [
        'process-timeout'         => 'setProcessTimeout',
        'process-async-wait'      => 'setProcessAsyncWait',
        'process-async-limit'     => 'setProcessAsyncLimit',
        'stop-on-failure'         => 'setStopOnFailure',
        'ignore-unstaged-changes' => 'setIgnoreUnstagedChanges',
        'strict'                  => 'setStrict',
        'skip-success-output'     => 'setSkipSuccessOutput',
        'message'                 => 'setMessage',
        'can-run-in'              => 'setCanRunIn',
    ];

    /**
     * @var \ClickNow\Checker\Config\Checker
     */
    private $checker;

    /**
     * @var array|bool
     */
    private $canRunIn;

    /**
     * @var null|float
     */
    private $processTimeout;

    /**
     * @var int
     */
    private $processAsyncWait;

    /**
     * @var int
     */
    private $processAsyncLimit;

    /**
     * @var bool
     */
    private $stopOnFailure;

    /**
     * @var bool
     */
    private $ignoreUnstagedChanges;

    /**
     * @var bool
     */
    private $strict;

    /**
     * @var bool
     */
    private $skipSuccessOutput;

    /**
     * @var array
     */
    private $message;

    /**
     * Config runner.
     *
     * @param \ClickNow\Checker\Config\Checker $checker
     */
    public function __construct(Checker $checker)
    {
        $this->checker = $checker;
    }

    /**
     * Can run in context?
     *
     * @param \ClickNow\Checker\Runner\RunnerInterface   $runner
     * @param \ClickNow\Checker\Context\ContextInterface $context
     *
     * @return bool
     */
    public function canRunInContext(RunnerInterface $runner, ContextInterface $context)
    {
        $option = isset($this->canRunIn) ? $this->canRunIn : true;

        if (is_array($option)) {
            return in_array($context->getRunner()->getName(), $option) || in_array($runner->getName(), $option);
        }

        return (bool) $option;
    }

    /**
     * Set can run in.
     *
     * @param array|bool $canRunIn
     *
     * @return void
     */
    public function setCanRunIn($canRunIn)
    {
        $this->canRunIn = is_array($canRunIn) ? (array) $canRunIn : (bool) $canRunIn;
    }

    /**
     * Get process timeout.
     *
     * @return float
     */
    public function getProcessTimeout()
    {
        if (!isset($this->processTimeout)) {
            $this->setProcessTimeout($this->checker->getProcessTimeout());
        }

        return $this->processTimeout;
    }

    /**
     * Set process timeout.
     *
     * @param float $processTimeout
     *
     * @return void
     */
    public function setProcessTimeout($processTimeout)
    {
        $this->processTimeout = (float) $processTimeout;
    }

    /**
     * Get process async wait.
     *
     * @return int
     */
    public function getProcessAsyncWait()
    {
        if (!isset($this->processAsyncWait)) {
            $this->setProcessAsyncWait($this->checker->getProcessAsyncWait());
        }

        return (int) $this->processAsyncWait;
    }

    /**
     * Set process async wait.
     *
     * @param int $processAsyncWait
     *
     * @return void
     */
    public function setProcessAsyncWait($processAsyncWait)
    {
        $this->processAsyncWait = (int) $processAsyncWait;
    }

    /**
     * Get process async limit.
     *
     * @return int
     */
    public function getProcessAsyncLimit()
    {
        if (!isset($this->processAsyncLimit)) {
            $this->setProcessAsyncLimit($this->checker->getProcessAsyncLimit());
        }

        return (int) $this->processAsyncLimit;
    }

    /**
     * Set process async limit.
     *
     * @param int $processAsyncLimit
     *
     * @return void
     */
    public function setProcessAsyncLimit($processAsyncLimit)
    {
        $this->processAsyncLimit = (int) $processAsyncLimit;
    }

    /**
     * Is stop on failure?
     *
     * @return bool
     */
    public function isStopOnFailure()
    {
        if (!isset($this->stopOnFailure)) {
            $this->setStopOnFailure($this->checker->isStopOnFailure());
        }

        return (bool) $this->stopOnFailure;
    }

    /**
     * Set stop on failure.
     *
     * @param bool $stopOnFailure
     *
     * @return void
     */
    public function setStopOnFailure($stopOnFailure)
    {
        $this->stopOnFailure = (bool) $stopOnFailure;
    }

    /**
     * Is ignore unstaged changes?
     *
     * @return bool
     */
    public function isIgnoreUnstagedChanges()
    {
        if (!isset($this->ignoreUnstagedChanges)) {
            $this->setIgnoreUnstagedChanges($this->checker->isIgnoreUnstagedChanges());
        }

        return (bool) $this->ignoreUnstagedChanges;
    }

    /**
     * Set ignore unstaged changes.
     *
     * @param bool $ignoreUnstagedChanges
     *
     * @return void
     */
    public function setIgnoreUnstagedChanges($ignoreUnstagedChanges)
    {
        $this->ignoreUnstagedChanges = (bool) $ignoreUnstagedChanges;
    }

    /**
     * Is strict?
     *
     * @return bool
     */
    public function isStrict()
    {
        if (!isset($this->strict)) {
            $this->setStrict($this->checker->isStrict());
        }

        return (bool) $this->strict;
    }

    /**
     * Set strict.
     *
     * @param bool $strict
     *
     * @return void
     */
    public function setStrict($strict)
    {
        $this->strict = (bool) $strict;
    }

    /**
     * Is skip success output?
     *
     * @return bool
     */
    public function isSkipSuccessOutput()
    {
        if (!isset($this->skipSuccessOutput)) {
            $this->setSkipSuccessOutput($this->checker->isSkipSuccessOutput());
        }

        return (bool) $this->skipSuccessOutput;
    }

    /**
     * Set skip success output.
     *
     * @param bool $skipSuccessOutput
     *
     * @return void
     */
    public function setSkipSuccessOutput($skipSuccessOutput)
    {
        $this->skipSuccessOutput = (bool) $skipSuccessOutput;
    }

    /**
     * Get message.
     *
     * @param string $resource
     *
     * @return null|string
     */
    public function getMessage($resource)
    {
        if (!array_key_exists($resource, (array) $this->message)) {
            return $this->checker->getMessage($resource);
        }

        return (string) $this->message[$resource];
    }

    /**
     * Set message.
     *
     * @param array $message
     *
     * @return void
     */
    public function setMessage(array $message)
    {
        $this->message = (array) $message;
    }
}
