<?php

namespace ClickNow\Checker\Command;

use ClickNow\Checker\Action\ActionInterface;
use ClickNow\Checker\Context\ContextInterface;
use ClickNow\Checker\Exception\ActionInvalidResultException;
use ClickNow\Checker\Exception\RuntimeException;
use ClickNow\Checker\Result\Result;
use ClickNow\Checker\Result\ResultInterface;
use ClickNow\Checker\Result\ResultsCollection;

abstract class AbstractCommandRunner implements CommandInterface
{
    /**
     * Run this command.
     *
     * @param \ClickNow\Checker\Command\CommandInterface $command
     * @param \ClickNow\Checker\Context\ContextInterface $context
     *
     * @return \ClickNow\Checker\Result\ResultInterface
     */
    public function run(CommandInterface $command, ContextInterface $context)
    {
        $actions = $this->getActionsToRun($context);
        $results = new ResultsCollection();

        foreach ($actions as $action) {
            $result = $this->runAction($context, $action);
            $results->add($result);
            if ($result->isError() && $this->shouldStopOnFailure()) {
                break;
            }
        }

        $status = $this->getResultStatusFromResults($results);
        $message = implode(PHP_EOL, $results->getAllMessages());

        return new Result($status, $command, $context, $this, $message);
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
        if ($results->isSuccessfully()) {
            return Result::SUCCESS;
        }

        if ($results->isFailed()) {
            return Result::ERROR;
        }

        return Result::WARNING;
    }

    /**
     * Run by action for this command.
     *
     * @param \ClickNow\Checker\Context\ContextInterface $context
     * @param \ClickNow\Checker\Action\ActionInterface   $action
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
        } catch (RuntimeException $e) {
            $result = Result::error($this, $context, $action, $e->getMessage());
        }

        return $this->parseActionResult($action, $result);
    }

    /**
     * Parse action result.
     *
     * @param \ClickNow\Checker\Action\ActionInterface $action
     * @param \ClickNow\Checker\Result\ResultInterface $result
     *
     * @return \ClickNow\Checker\Result\ResultInterface
     */
    private function parseActionResult(ActionInterface $action, ResultInterface $result)
    {
        if (!$result->isSuccess() && !$this->isBlockingAction($action)) {
            return Result::warning(
                $result->getCommand(),
                $result->getContext(),
                $result->getAction(),
                $result->getMessage()
            );
        }

        return $result;
    }
}
