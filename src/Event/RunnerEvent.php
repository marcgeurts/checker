<?php

namespace ClickNow\Checker\Event;

use ClickNow\Checker\Context\ContextInterface;
use ClickNow\Checker\Result\ResultsCollection;
use ClickNow\Checker\Runner\ActionsCollection;
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
     * @var \ClickNow\Checker\Runner\ActionsCollection
     */
    private $actions;

    /**
     * @var \ClickNow\Checker\Result\ResultsCollection|null
     */
    private $results;

    /**
     * Runner event.
     *
     * @param \ClickNow\Checker\Context\ContextInterface      $context
     * @param \ClickNow\Checker\Runner\ActionsCollection      $actions
     * @param \ClickNow\Checker\Result\ResultsCollection|null $results
     */
    public function __construct(
        ContextInterface $context,
        ActionsCollection $actions,
        ResultsCollection $results = null
    ) {
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
     * Get actions.
     *
     * @return \ClickNow\Checker\Runner\ActionsCollection
     */
    public function getActions()
    {
        return $this->actions;
    }

    /**
     * Get results.
     *
     * @return \ClickNow\Checker\Result\ResultsCollection|null
     */
    public function getResults()
    {
        return $this->results;
    }
}
