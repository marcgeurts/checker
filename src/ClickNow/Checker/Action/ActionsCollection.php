<?php

namespace ClickNow\Checker\Action;

use ClickNow\Checker\Context\ContextInterface;
use ClickNow\Checker\Runner\RunnerInterface;
use Doctrine\Common\Collections\ArrayCollection;
use SplPriorityQueue;

class ActionsCollection extends ArrayCollection
{
    /**
     * Filter by context.
     *
     * @param \ClickNow\Checker\Runner\RunnerInterface   $runner
     * @param \ClickNow\Checker\Context\ContextInterface $context
     *
     * @return \ClickNow\Checker\Action\ActionsCollection
     */
    public function filterByContext(RunnerInterface $runner, ContextInterface $context)
    {
        return $this->filter(function (ActionInterface $action) use ($runner, $context) {
            return $action->canRunInContext($runner, $context);
        });
    }

    /**
     * Sort by priority.
     *
     * @param \ClickNow\Checker\Runner\RunnerInterface $runner
     *
     * @return \ClickNow\Checker\Action\ActionsCollection
     */
    public function sortByPriority(RunnerInterface $runner)
    {
        $priorityQueue = new SplPriorityQueue();
        $stableSortIndex = PHP_INT_MAX;

        foreach ($this->getIterator() as $action) {
            $priorityQueue->insert($action, [$runner->getActionPriority($action), $stableSortIndex--]);
        }

        return new self(array_values(iterator_to_array($priorityQueue)));
    }
}
