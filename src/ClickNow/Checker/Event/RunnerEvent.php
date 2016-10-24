<?php

namespace ClickNow\Checker\Event;

use ClickNow\Checker\Action\ActionsCollection;
use ClickNow\Checker\Context\ContextInterface;
use ClickNow\Checker\Result\ResultsCollection;
use Symfony\Component\EventDispatcher\Event;

class RunnerEvent extends Event
{
    const RUNNER_RUN = 'checker.runner.run';
    const RUNNER_SUCCESSFULLY = 'checker.runner.successfully';
    const RUNNER_FAILED = 'checker.runner.failed';

    /**
     * @var \ClickNow\Checker\Context\ContextInterface
     */
    private $context;

    /**
     * @var \ClickNow\Checker\Action\ActionsCollection
     */
    private $actions;

    /**
     * @var \ClickNow\Checker\Result\ResultsCollection
     */
    private $results;

    /**
     * Runner event.
     *
     * @param \ClickNow\Checker\Context\ContextInterface $context
     * @param \ClickNow\Checker\Action\ActionsCollection $actions
     * @param \ClickNow\Checker\Result\ResultsCollection $results
     */
    public function __construct(ContextInterface $context, ActionsCollection $actions, ResultsCollection $results)
    {
        $this->context = $context;
        $this->actions = $actions;
        $this->results = $results;
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
     * Get actions collection.
     *
     * @return \ClickNow\Checker\Action\ActionsCollection
     */
    public function getActions()
    {
        return $this->actions;
    }

    /**
     * Get results collection.
     *
     * @return \ClickNow\Checker\Result\ResultsCollection
     */
    public function getResults()
    {
        return $this->results;
    }
}
