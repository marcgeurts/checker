<?php

namespace ClickNow\Checker\Runner;

use ClickNow\Checker\Action\ActionInterface;
use ClickNow\Checker\Action\ActionsCollection;
use ClickNow\Checker\Config\Checker;
use ClickNow\Checker\Context\ContextInterface;
use ClickNow\Checker\Exception\ActionAlreadyRegisteredException;
use ClickNow\Checker\Exception\ActionInvalidResultException;
use ClickNow\Checker\Exception\ActionNotFoundException;
use ClickNow\Checker\Exception\PlatformException;
use ClickNow\Checker\Exception\RuntimeException;
use ClickNow\Checker\Result\Result;
use ClickNow\Checker\Result\ResultInterface;
use ClickNow\Checker\Result\ResultsCollection;
use Symfony\Component\OptionsResolver\OptionsResolver;

class Runner implements RunnerInterface
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
     * Runner.
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
            'process-timeout'         => $this->checker->getProcessTimeout(),
            'process-async-wait'      => $this->checker->getProcessAsyncWait(),
            'process-async-limit'     => $this->checker->getProcessAsyncLimit(),
            'stop-on-failure'         => $this->checker->isStopOnFailure(),
            'ignore-unstaged-changes' => $this->checker->isIgnoreUnstagedChanges(),
            'skip-success-output'     => $this->checker->isSkipSuccessOutput(),
            'message'                 => [],
            'can-run-in'              => true,
        ], $this->options));
        $resolver->setAllowedTypes('process-timeout', ['float', 'int', 'null']);
        $resolver->setAllowedTypes('process-async-wait', ['int']);
        $resolver->setAllowedTypes('process-async-limit', ['int']);
        $resolver->setAllowedTypes('stop-on-failure', ['bool']);
        $resolver->setAllowedTypes('ignore-unstaged-changes', ['bool']);
        $resolver->setAllowedTypes('skip-success-output', ['bool']);
        $resolver->setAllowedTypes('message', ['array']);
        $resolver->setAllowedTypes('can-run-in', ['array', 'bool']);
        $this->options = $resolver->resolve($config);
    }

    /**
     * Get process timeout.
     *
     * @return null|float|int
     */
    public function getProcessTimeout()
    {
        return $this->options['process-timeout'];
    }

    /**
     * Get process async wait.
     *
     * @return int
     */
    public function getProcessAsyncWait()
    {
        return (int) $this->options['process-async-wait'];
    }

    /**
     * Get process async limit.
     *
     * @return int
     */
    public function getProcessAsyncLimit()
    {
        return (int) $this->options['process-async-limit'];
    }

    /**
     * Is stop on failure?
     *
     * @return bool
     */
    public function isStopOnFailure()
    {
        return (bool) $this->options['stop-on-failure'];
    }

    /**
     * Is ignore unstaged changes?
     *
     * @return bool
     */
    public function isIgnoreUnstagedChanges()
    {
        return (bool) $this->options['ignore-unstaged-changes'];
    }

    /**
     * Is skip success output?
     *
     * @return bool
     */
    public function isSkipSuccessOutput()
    {
        return (bool) $this->options['skip-success-output'];
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
     * @param \ClickNow\Checker\Runner\RunnerInterface   $runner
     * @param \ClickNow\Checker\Context\ContextInterface $context
     *
     * @return bool
     */
    public function canRunInContext(RunnerInterface $runner, ContextInterface $context)
    {
        $option = $this->options['can-run-in'];

        if (is_array($option)) {
            return in_array($context->getRunner()->getName(), $option) || in_array($runner->getName(), $option);
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
            return Result::success($runner, $context, $this);
        }

        $results = $this->runActions($context, $actions);
        $status = $this->getResultStatusFromResults($results);
        $messages = $results->getAllMessages();
        $message = empty($messages) ? null : implode(PHP_EOL, $messages);

        return new Result($status, $runner, $context, $this, $message);
    }

    /**
     * Run actions.
     *
     * @param \ClickNow\Checker\Context\ContextInterface $context
     * @param \ClickNow\Checker\Action\ActionsCollection $actions
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
        if ($results->isSuccessfully()) {
            return Result::SUCCESS;
        }

        if ($results->isFailed()) {
            return Result::ERROR;
        }

        return Result::WARNING;
    }

    /**
     * Run action.
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
        } catch (PlatformException $e) {
            $result = Result::warning($this, $context, $action, $e->getMessage());
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
        if (!$result->isSuccess() && !$this->isActionBlocking($action)) {
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
