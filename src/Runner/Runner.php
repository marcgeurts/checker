<?php

namespace ClickNow\Checker\Runner;

use ClickNow\Checker\Config\Checker;
use ClickNow\Checker\Context\ContextInterface;
use ClickNow\Checker\Exception\ActionInvalidResultException;
use ClickNow\Checker\Exception\PlatformException;
use ClickNow\Checker\Exception\RuntimeException;
use ClickNow\Checker\Result\Result;
use ClickNow\Checker\Result\ResultInterface;
use ClickNow\Checker\Result\ResultsCollection;

class Runner implements RunnerInterface
{
    use ActionRunner, ConfigRunner {
        ActionRunner::__construct as private __actionRunnerConstruct;
        ConfigRunner::__construct as private __configRunnerConstruct;
    }

    /**
     * @var string
     */
    private $name;

    /**
     * Runner.
     *
     * @param \ClickNow\Checker\Config\Checker $checker
     * @param string                           $name
     */
    public function __construct(Checker $checker, $name)
    {
        $this->__actionRunnerConstruct(new ActionsCollection());
        $this->__configRunnerConstruct($checker);
        $this->name = $name;
    }

    /**
     * Get name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Get actions to run.
     *
     * @param \ClickNow\Checker\Context\ContextInterface $context
     *
     * @return \ClickNow\Checker\Runner\ActionsCollection
     */
    public function getActionsToRun(ContextInterface $context)
    {
        return $this->actions->filterByContext($this, $context)->sortByPriority($this);
    }

    /**
     * Run.
     *
     * @param \ClickNow\Checker\Runner\RunnerInterface   $runner
     * @param \ClickNow\Checker\Context\ContextInterface $context
     *
     * @return \ClickNow\Checker\Result\ResultInterface
     */
    public function run(RunnerInterface $runner, ContextInterface $context)
    {
        $actions = $this->getActionsToRun($context);

        if ($actions->isEmpty()) {
            return Result::skipped($runner, $context, $this);
        }

        $results = $this->runActions($context, $actions);
        $status = $this->getResultStatusFromResults($results);
        $messages = $results->getAllMessages();
        $message = empty($messages) ? null : implode(PHP_EOL, $messages);

        return new Result($status, $runner, $context, $this, $message);
    }

    /**
     * Run action.
     *
     * @param \ClickNow\Checker\Context\ContextInterface $context
     * @param \ClickNow\Checker\Runner\ActionInterface   $action
     *
     * @return \ClickNow\Checker\Result\ResultInterface
     */
    public function runAction(ContextInterface $context, ActionInterface $action)
    {
        try {
            $result = $action->run($this, $context);

            if (!$result instanceof ResultInterface) {
                throw new ActionInvalidResultException($action->getName());
            }
        } catch (PlatformException $e) {
            $result = Result::warning($this, $context, $action, $e->getMessage());
        } catch (RuntimeException $e) {
            $result = Result::error($this, $context, $action, $e->getMessage());
        }

        return $this->parseActionResult($action, $result);
    }

    /**
     * Run actions.
     *
     * @param \ClickNow\Checker\Context\ContextInterface $context
     * @param \ClickNow\Checker\Runner\ActionsCollection $actions
     *
     * @return \ClickNow\Checker\Result\ResultsCollection
     */
    private function runActions(ContextInterface $context, ActionsCollection $actions)
    {
        $results = new ResultsCollection();

        foreach ($actions as $action) {
            $result = $this->runAction($context, $action);
            $results->add($result);
            if ($result->isError() && $this->isStopOnFailure()) {
                break;
            }
        }

        return $results;
    }

    /**
     * Get result status from results.
     *
     * @param \ClickNow\Checker\Result\ResultsCollection $results
     *
     * @return int
     */
    private function getResultStatusFromResults(ResultsCollection $results)
    {
        if ($results->isFailed($this->isStrict())) {
            return Result::ERROR;
        }

        if (!$results->filterByWarning()->isEmpty()) {
            return Result::WARNING;
        }

        if (!$results->filterBySuccess()->isEmpty()) {
            return Result::SUCCESS;
        }

        return Result::SKIPPED;
    }

    /**
     * Parse action result.
     *
     * @param \ClickNow\Checker\Runner\ActionInterface $action
     * @param \ClickNow\Checker\Result\ResultInterface $result
     *
     * @return \ClickNow\Checker\Result\ResultInterface
     */
    private function parseActionResult(ActionInterface $action, ResultInterface $result)
    {
        if ($result->isError() && !$this->isActionBlocking($action)) {
            return Result::warning(
                $result->getRunner(),
                $result->getContext(),
                $result->getAction(),
                $result->getMessage()
            );
        }

        return $result;
    }
}
