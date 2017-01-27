<?php

namespace ClickNow\Checker\Helper;

use ClickNow\Checker\Context\ContextInterface;
use ClickNow\Checker\Event\ActionEvent;
use ClickNow\Checker\Event\RunnerEvent;
use ClickNow\Checker\IO\IOInterface;
use ClickNow\Checker\Result\ResultsCollection;
use ClickNow\Checker\Runner\ActionInterface;
use ClickNow\Checker\Runner\ActionsCollection;
use Symfony\Component\Console\Helper\Helper;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class RunnerHelper extends Helper
{
    const HELPER_NAME = 'runner';
    const CODE_SUCCESS = 0;
    const CODE_ERROR = 1;

    /**
     * @var \Symfony\Component\EventDispatcher\EventDispatcherInterface
     */
    private $dispatcher;

    /**
     * @var \ClickNow\Checker\IO\IOInterface
     */
    private $io;

    /**
     * Runner helper.
     *
     * @param \Symfony\Component\EventDispatcher\EventDispatcherInterface $dispatcher
     * @param \ClickNow\Checker\IO\IOInterface                            $io
     */
    public function __construct(EventDispatcherInterface $dispatcher, IOInterface $io)
    {
        $this->dispatcher = $dispatcher;
        $this->io = $io;
    }

    /**
     * Run.
     *
     * @param \ClickNow\Checker\Context\ContextInterface $context
     *
     * @return int
     */
    public function run(ContextInterface $context)
    {
        $this->io->title(sprintf('Checker is analyzing your code by `%s`!', $context->getRunner()->getName()));

        $actions = $context->getRunner()->getActionsToRun($context);

        if ($actions->isEmpty()) {
            $this->io->note('No actions available.');

            return self::CODE_SUCCESS;
        }

        return $this->doRun($context, $actions);
    }

    /**
     * Do run.
     *
     * @param \ClickNow\Checker\Context\ContextInterface $context
     * @param \ClickNow\Checker\Runner\ActionsCollection $actions
     *
     * @return int
     */
    private function doRun(ContextInterface $context, ActionsCollection $actions)
    {
        $this->dispatcher->dispatch(RunnerEvent::RUNNER_RUN, new RunnerEvent($context, $actions));
        $results = $this->runActions($context, $actions);

        if ($results->isFailed()) {
            $this->dispatcher->dispatch(RunnerEvent::RUNNER_FAILED, new RunnerEvent($context, $actions, $results));

            return self::CODE_ERROR;
        }

        $this->dispatcher->dispatch(RunnerEvent::RUNNER_SUCCESSFULLY, new RunnerEvent($context, $actions, $results));

        return self::CODE_SUCCESS;
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
            if ($result->isError() && $context->getRunner()->isStopOnFailure()) {
                break;
            }
        }

        return $results;
    }

    /**
     * Run action.
     *
     * @param \ClickNow\Checker\Context\ContextInterface $context
     * @param \ClickNow\Checker\Runner\ActionInterface   $action
     *
     * @return \ClickNow\Checker\Result\ResultInterface
     */
    private function runAction(ContextInterface $context, ActionInterface $action)
    {
        $this->io->log(sprintf('Checker running action `%s`.', $action->getName()));

        $this->dispatcher->dispatch(ActionEvent::ACTION_RUN, new ActionEvent($context, $action));

        $result = $context->getRunner()->runAction($context, $action);

        if (!$result->isSuccess()) {
            $this->dispatcher->dispatch(ActionEvent::ACTION_FAILED, new ActionEvent($context, $action, $result));

            return $result;
        }

        $this->dispatcher->dispatch(ActionEvent::ACTION_SUCCESSFULLY, new ActionEvent($context, $action, $result));

        return $result;
    }

    /**
     * Get name.
     *
     * @return string
     */
    public function getName()
    {
        return self::HELPER_NAME;
    }
}
