<?php

namespace ClickNow\Checker\Console\Helper;

use ClickNow\Checker\Action\ActionInterface;
use ClickNow\Checker\Context\ContextInterface;
use ClickNow\Checker\Event\ActionEvent;
use ClickNow\Checker\Event\RunnerEvent;
use ClickNow\Checker\IO\IOInterface;
use ClickNow\Checker\Result\ResultsCollection;
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
    private $eventDispatcher;

    /**
     * @var \ClickNow\Checker\IO\IOInterface
     */
    private $io;

    /**
     * Runner helper.
     *
     * @param \Symfony\Component\EventDispatcher\EventDispatcherInterface $eventDispatcher
     * @param \ClickNow\Checker\IO\IOInterface                            $io
     */
    public function __construct(EventDispatcherInterface $eventDispatcher, IOInterface $io)
    {
        $this->eventDispatcher = $eventDispatcher;
        $this->io = $io;
    }

    /**
     * Run.
     *
     * @param \ClickNow\Checker\Context\ContextInterface $context
     *
     * @return int|void
     */
    public function run(ContextInterface $context)
    {
        $this->io->title(sprintf(
            'Checker is analyzing your code by action `%s`!',
            $context->getCommand()->getName()
        ));

        $results = new ResultsCollection();
        $actions = $context->getCommand()->getActionsToRun($context);

        if ($actions->isEmpty()) {
            $this->io->note('No actions available.');

            return;
        }

        $this->eventDispatcher->dispatch(
            RunnerEvent::RUNNER_RUN,
            new RunnerEvent($context, $actions, $results)
        );

        foreach ($actions as $action) {
            $result = $this->runAction($context, $action);
            $results->add($result);
            if ($result->isError() && $context->getCommand()->stopOnFailure()) {
                break;
            }
        }

        if ($results->isFailed()) {
            $this->eventDispatcher->dispatch(
                RunnerEvent::RUNNER_FAILED,
                new RunnerEvent($context, $actions, $results)
            );

            return self::CODE_ERROR;
        }

        $this->eventDispatcher->dispatch(
            RunnerEvent::RUNNER_SUCCESSFULLY,
            new RunnerEvent($context, $actions, $results)
        );

        return self::CODE_SUCCESS;
    }

    /**
     * Run actions by command and dispatch their respective events.
     *
     * @param \ClickNow\Checker\Context\ContextInterface $context
     * @param \ClickNow\Checker\Action\ActionInterface   $action
     *
     * @return \ClickNow\Checker\Result\ResultInterface
     */
    private function runAction(ContextInterface $context, ActionInterface $action)
    {
        $this->io->log(sprintf('Checker running action `%s`.', $action->getName()));

        $this->eventDispatcher->dispatch(
            ActionEvent::ACTION_RUN,
            new ActionEvent($context, $action)
        );

        $result = $context->getCommand()->runAction($context, $action);

        if (!$result->isSuccess()) {
            $this->eventDispatcher->dispatch(
                ActionEvent::ACTION_FAILED,
                new ActionEvent($context, $action, $result)
            );

            return $result;
        }

        $this->eventDispatcher->dispatch(
            ActionEvent::ACTION_SUCCESSFULLY,
            new ActionEvent($context, $action, $result)
        );

        return $result;
    }

    /**
     * Get helper name.
     *
     * @return string
     */
    public function getName()
    {
        return self::HELPER_NAME;
    }
}
