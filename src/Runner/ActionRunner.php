<?php

namespace ClickNow\Checker\Runner;

use ClickNow\Checker\Exception\ActionAlreadyRegisteredException;
use ClickNow\Checker\Exception\ActionNotFoundException;
use Symfony\Component\OptionsResolver\OptionsResolver;

trait ActionRunner
{
    /**
     * @var \ClickNow\Checker\Runner\ActionsCollection
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
     * Action runner.
     *
     * @param \ClickNow\Checker\Runner\ActionsCollection $actions
     */
    public function __construct(ActionsCollection $actions)
    {
        $this->actions = $actions;
    }

    /**
     * Get actions.
     *
     * @return \ClickNow\Checker\Runner\ActionsCollection
     */
    public function getActions()
    {
        return $this->actions;
    }

    /**
     * Add action.
     *
     * @param \ClickNow\Checker\Runner\ActionInterface $action
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
     * @param \ClickNow\Checker\Runner\ActionInterface $action
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
     * Get action metadata.
     *
     * @param \ClickNow\Checker\Runner\ActionInterface $action
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
     * @param \ClickNow\Checker\Runner\ActionInterface $action
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
     * @param \ClickNow\Checker\Runner\ActionInterface $action
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
     * @param \ClickNow\Checker\Runner\ActionInterface $action
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
}
