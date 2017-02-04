<?php

namespace ClickNow\Checker\Event;

use ClickNow\Checker\Context\ContextInterface;
use ClickNow\Checker\Result\ResultInterface;
use ClickNow\Checker\Runner\ActionInterface;
use Symfony\Component\EventDispatcher\Event;

class ActionEvent extends Event
{
    const ACTION_RUN = 'checker.action.run';
    const ACTION_SUCCESSFULLY = 'checker.action.successfully';
    const ACTION_FAILED = 'checker.action.failed';

    /**
     * @var \ClickNow\Checker\Context\ContextInterface
     */
    private $context;

    /**
     * @var \ClickNow\Checker\Runner\ActionInterface
     */
    private $action;

    /**
     * @var \ClickNow\Checker\Result\ResultInterface|null
     */
    private $result;

    /**
     * Action event.
     *
     * @param \ClickNow\Checker\Context\ContextInterface    $context
     * @param \ClickNow\Checker\Runner\ActionInterface      $action
     * @param \ClickNow\Checker\Result\ResultInterface|null $result
     */
    public function __construct(ContextInterface $context, ActionInterface $action, ResultInterface $result = null)
    {
        $this->context = $context;
        $this->action = $action;
        $this->result = $result;
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
     * @return \ClickNow\Checker\Runner\ActionInterface
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * Get result.
     *
     * @return \ClickNow\Checker\Result\ResultInterface|null
     */
    public function getResult()
    {
        return $this->result;
    }
}
