<?php

namespace ClickNow\Checker\Command;

use ClickNow\Checker\Action\ActionInterface;
use ClickNow\Checker\Action\ActionsCollection;
use ClickNow\Checker\Config\Checker;
use ClickNow\Checker\Context\ContextInterface;
use ClickNow\Checker\Exception\ActionAlreadyRegisteredException;
use ClickNow\Checker\Exception\ActionNotFoundException;
use Symfony\Component\OptionsResolver\OptionsResolver;

class Command extends AbstractCommandRunner
{
    /**
     * @var \ClickNow\Checker\Config\Checker
     */
    private $checker;

    /**
     * @var string
     */
    private $name;

    /**
     * @var \ClickNow\Checker\Action\ActionsCollection
     */
    private $actions;

    /**
     * @var array
     */
    private $actionsMetadata = [];

    /**
     * @var array
     */
    private $actionsConfig = [];

    /**
     * @var array
     */
    private $options = [];

    /**
     * Command.
     *
     * @param \ClickNow\Checker\Config\Checker $checker
     * @param string                           $name
     */
    public function __construct(Checker $checker, $name)
    {
        $this->checker = $checker;
        $this->name = $name;
        $this->actions = new ActionsCollection();
        $this->setConfig();
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
     * Get actions.
     *
     * @return \ClickNow\Checker\Action\ActionsCollection
     */
    public function getActions()
    {
        return $this->actions;
    }

    /**
     * Add action.
     *
     * @param \ClickNow\Checker\Action\ActionInterface $action
     * @param array                                    $config
     *
     * @throws \ClickNow\Checker\Exception\ActionAlreadyRegisteredException
     *
     * @return void
     */
    public function addAction(ActionInterface $action, array $config = [])
    {
        $name = $action->getName();

        if ($this->hasAction($action)) {
            throw new ActionAlreadyRegisteredException($name);
        }

        $metadata = isset($config['metadata']) ? (array) $config['metadata'] : [];
        unset($config['metadata']);

        $this->actionsMetadata[$name] = $this->parseActionMetadata($metadata);
        $this->actionsConfig[$name] = (array) $config;
        $this->actions->add($action);
    }

    /**
     * Has action?
     *
     * @param \ClickNow\Checker\Action\ActionInterface $action
     *
     * @return bool
     */
    public function hasAction(ActionInterface $action)
    {
        $metadata = array_key_exists($action->getName(), $this->actionsMetadata);
        $config = array_key_exists($action->getName(), $this->actionsConfig);

        return $this->actions->contains($action) || $metadata || $config;
    }

    /**
     * Parse action metadata.
     *
     * @param array $metadata
     *
     * @return array
     */
    private function parseActionMetadata(array $metadata = [])
    {
        $resolver = new OptionsResolver();
        $resolver->setDefaults([
            'priority' => 0,
            'blocking' => true,
        ]);
        $resolver->setAllowedTypes('priority', ['int']);
        $resolver->setAllowedTypes('blocking', ['bool']);

        return $resolver->resolve($metadata);
    }

    /**
     * Set config.
     *
     * @param array $config
     *
     * @return void
     */
    public function setConfig(array $config = [])
    {
        $resolver = new OptionsResolver();
        $resolver->setDefaults(array_merge([
            'process_timeout'         => $this->checker->getProcessTimeout(),
            'process_async_wait'      => $this->checker->getProcessAsyncWait(),
            'process_async_limit'     => $this->checker->getProcessAsyncLimit(),
            'stop_on_failure'         => $this->checker->isStopOnFailure(),
            'ignore_unstaged_changes' => $this->checker->isIgnoreUnstagedChanges(),
            'skip_success_output'     => $this->checker->isSkipSuccessOutput(),
            'message'                 => [],
            'can_run_in'              => true,
        ], $this->options));
        $resolver->setAllowedTypes('process_timeout', ['float', 'int', 'null']);
        $resolver->setAllowedTypes('process_async_wait', ['int']);
        $resolver->setAllowedTypes('process_async_limit', ['int']);
        $resolver->setAllowedTypes('stop_on_failure', ['bool']);
        $resolver->setAllowedTypes('ignore_unstaged_changes', ['bool']);
        $resolver->setAllowedTypes('skip_success_output', ['bool']);
        $resolver->setAllowedTypes('message', ['array']);
        $resolver->setAllowedTypes('can_run_in', ['array', 'bool']);
        $this->options = $resolver->resolve($config);
    }

    /**
     * Get process timeout.
     *
     * @return null|float|int
     */
    public function getProcessTimeout()
    {
        return $this->options['process_timeout'];
    }

    /**
     * Get process async wait.
     *
     * @return int
     */
    public function getProcessAsyncWait()
    {
        return (int) $this->options['process_async_wait'];
    }

    /**
     * Get process async limit.
     *
     * @return int
     */
    public function getProcessAsyncLimit()
    {
        return (int) $this->options['process_async_limit'];
    }

    /**
     * Is stop on failure?
     *
     * @return bool
     */
    public function isStopOnFailure()
    {
        return (bool) $this->options['stop_on_failure'];
    }

    /**
     * Is ignore unstaged changes?
     *
     * @return bool
     */
    public function isIgnoreUnstagedChanges()
    {
        return (bool) $this->options['ignore_unstaged_changes'];
    }

    /**
     * Is skip success output?
     *
     * @return bool
     */
    public function isSkipSuccessOutput()
    {
        return (bool) $this->options['skip_success_output'];
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
        $messages = (array) $this->options['message'];

        if (array_key_exists($resource, $messages)) {
            return (string) $messages[$resource];
        }

        return $this->checker->getMessage($resource);
    }

    /**
     * Can run in context?
     *
     * @param \ClickNow\Checker\Command\CommandInterface $command
     * @param \ClickNow\Checker\Context\ContextInterface $context
     *
     * @return bool
     */
    public function canRunInContext(CommandInterface $command, ContextInterface $context)
    {
        $option = $this->options['can_run_in'];

        if (is_array($option)) {
            return in_array($context->getCommand()->getName(), $option) || in_array($command->getName(), $option);
        }

        return (bool) $option;
    }

    /**
     * Get action metadata.
     *
     * @param \ClickNow\Checker\Action\ActionInterface $action
     *
     * @throws \ClickNow\Checker\Exception\ActionNotFoundException
     *
     * @return array
     */
    public function getActionMetadata(ActionInterface $action)
    {
        if (!$this->hasAction($action)) {
            throw new ActionNotFoundException($action->getName());
        }

        return (array) $this->actionsMetadata[$action->getName()];
    }

    /**
     * Get action priority.
     *
     * @param \ClickNow\Checker\Action\ActionInterface $action
     *
     * @return int
     */
    public function getActionPriority(ActionInterface $action)
    {
        return (int) $this->getActionMetadata($action)['priority'];
    }

    /**
     * Is action blocking?
     *
     * @param \ClickNow\Checker\Action\ActionInterface $action
     *
     * @return bool
     */
    public function isActionBlocking(ActionInterface $action)
    {
        return (bool) $this->getActionMetadata($action)['blocking'];
    }

    /**
     * Get action config.
     *
     * @param \ClickNow\Checker\Action\ActionInterface $action
     *
     * @throws \ClickNow\Checker\Exception\ActionNotFoundException
     *
     * @return array
     */
    public function getActionConfig(ActionInterface $action)
    {
        if (!$this->hasAction($action)) {
            throw new ActionNotFoundException($action->getName());
        }

        return (array) $this->actionsConfig[$action->getName()];
    }

    /**
     * Get actions to run.
     *
     * @param \ClickNow\Checker\Context\ContextInterface $context
     *
     * @return \ClickNow\Checker\Action\ActionsCollection
     */
    public function getActionsToRun(ContextInterface $context)
    {
        return $this->actions->filterByContext($this, $context)->sortByPriority($this);
    }
}
