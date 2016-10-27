<?php

namespace ClickNow\Checker\Command;

use ClickNow\Checker\Action\ActionInterface;
use ClickNow\Checker\Action\ActionsCollection;
use ClickNow\Checker\Config\Checker;
use ClickNow\Checker\Context\ContextInterface;
use ClickNow\Checker\Exception\ActionAlreadyRegisteredException;
use ClickNow\Checker\Exception\ActionInvalidResultException;
use ClickNow\Checker\Exception\RuntimeException;
use ClickNow\Checker\Result\Result;
use ClickNow\Checker\Result\ResultInterface;
use ClickNow\Checker\Result\ResultsCollection;
use Symfony\Component\OptionsResolver\OptionsResolver;

class Command implements CommandInterface
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
    private $actionsMetadata;

    /**
     * @var array
     */
    private $actionsConfig;

    /**
     * @var array
     */
    private $options;

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
        $this->actionsMetadata = [];
        $this->actionsConfig = [];
        $this->options = [];
    }

    /**
     * Get command name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Get actions for this command.
     *
     * @return \ClickNow\Checker\Action\ActionsCollection
     */
    public function getActions()
    {
        return $this->actions;
    }

    /**
     * Add action for this command.
     *
     * @param \ClickNow\Checker\Action\ActionInterface $action
     * @param array                                    $config
     *
     * @throws \ClickNow\Checker\Exception\ActionAlreadyRegisteredException
     *
     * @return void
     */
    public function addAction(ActionInterface $action, array $config)
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
     * Parse metadata by action for this command.
     *
     * @param array $metadata
     *
     * @return array
     */
    private function parseActionMetadata(array $metadata)
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
     * Set config for this command.
     *
     * @param array $config
     *
     * @return void
     */
    public function setConfig(array $config)
    {
        $resolver = new OptionsResolver();
        $resolver->setDefaults(array_merge([
            'process_timeout'         => $this->checker->getProcessTimeout(),
            'stop_on_failure'         => $this->checker->shouldStopOnFailure(),
            'ignore_unstaged_changes' => $this->checker->shouldIgnoreUnstagedChanges(),
            'skip_success_output'     => $this->checker->isSkipSuccessOutput(),
            'message'                 => [],
            'can_run_in'              => true,
        ], $this->options));
        $resolver->setAllowedTypes('process_timeout', ['float', 'null']);
        $resolver->setAllowedTypes('stop_on_failure', ['bool']);
        $resolver->setAllowedTypes('ignore_unstaged_changes', ['bool']);
        $resolver->setAllowedTypes('skip_success_output', ['bool']);
        $resolver->setAllowedTypes('message', ['array']);
        $resolver->setAllowedTypes('can_run_in', ['array', 'bool']);
        $this->options = $resolver->resolve($config);
    }

    /**
     * Get process timeout for this command.
     *
     * @return null|float
     */
    public function getProcessTimeout()
    {
        return $this->options['process_timeout'];
    }

    /**
     * Should stop running command on failure?
     *
     * @return bool
     */
    public function shouldStopOnFailure()
    {
        return $this->options['stop_on_failure'];
    }

    /**
     * Should ignore unstaged changes for this command?
     *
     * @return bool
     */
    public function shouldIgnoreUnstagedChanges()
    {
        return $this->options['ignore_unstaged_changes'];
    }

    /**
     * It is to skip the success output for this command?
     *
     * @return bool
     */
    public function isSkipSuccessOutput()
    {
        return $this->options['skip_success_output'];
    }

    /**
     * Get message by resource for this command.
     *
     * @param string $resource
     *
     * @return null|string
     */
    public function getMessage($resource)
    {
        if (array_key_exists($resource, $this->options['message'])) {
            return (string) $this->options['message'][$resource];
        }

        return $this->checker->getMessage($resource);
    }

    /**
     * Get metadata by action for this command.
     *
     * @param \ClickNow\Checker\Action\ActionInterface $action
     *
     * @return array
     */
    public function getActionMetadata(ActionInterface $action)
    {
        return (array) $this->actionsMetadata[$action->getName()];
    }

    /**
     * Get priority by action for this command.
     *
     * @param \ClickNow\Checker\Action\ActionInterface $action
     *
     * @return int
     */
    public function getPriorityAction(ActionInterface $action)
    {
        return (int) $this->getActionMetadata($action)['priority'];
    }

    /**
     * Is blocking action for this command?
     *
     * @param \ClickNow\Checker\Action\ActionInterface $action
     *
     * @return bool
     */
    public function isBlockingAction(ActionInterface $action)
    {
        return (bool) $this->getActionMetadata($action)['blocking'];
    }

    /**
     * Get config by action for this command.
     *
     * @param \ClickNow\Checker\Action\ActionInterface $action
     *
     * @return array
     */
    public function getActionConfig(ActionInterface $action)
    {
        return (array) $this->actionsConfig[$action->getName()];
    }

    /**
     * Get actions to run for this command.
     *
     * @param \ClickNow\Checker\Context\ContextInterface $context
     *
     * @return \ClickNow\Checker\Action\ActionsCollection
     */
    public function getActionsToRun(ContextInterface $context)
    {
        return $this->actions->filterByContext($this, $context)->sortByPriority($this);
    }

    /**
     * This command can run in context?
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
