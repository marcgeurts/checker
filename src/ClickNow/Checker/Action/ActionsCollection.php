<?php

namespace ClickNow\Checker\Action;

use ClickNow\Checker\Command\CommandInterface;
use ClickNow\Checker\Context\ContextInterface;
use Doctrine\Common\Collections\ArrayCollection;
use SplPriorityQueue;

class ActionsCollection extends ArrayCollection
{
    /**
     * Filter by context.
     *
     * @param \ClickNow\Checker\Command\CommandInterface $command
     * @param \ClickNow\Checker\Context\ContextInterface $context
     *
     * @return \ClickNow\Checker\Action\ActionsCollection
     */
    public function filterByContext(CommandInterface $command, ContextInterface $context)
    {
        return $this->filter(function (ActionInterface $action) use ($command, $context) {
            return $action->canRunInContext($command, $context);
        });
    }

    /**
     * Sort by priority.
     *
     * @param \ClickNow\Checker\Command\CommandInterface $command
     *
     * @return \ClickNow\Checker\Action\ActionsCollection
     */
    public function sortByPriority(CommandInterface $command)
    {
        $priorityQueue = new SplPriorityQueue();
        $stableSortIndex = PHP_INT_MAX;

        foreach ($this->getIterator() as $action) {
            $priorityQueue->insert($action, [$command->getActionPriority($action), $stableSortIndex--]);
        }

        return new self(array_values(iterator_to_array($priorityQueue)));
    }
}
