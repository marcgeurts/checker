<?php

namespace ClickNow\Checker\Config\Compiler;

use ClickNow\Checker\Command\Command;
use ClickNow\Checker\Exception\CommandAlreadyRegisteredException;
use ClickNow\Checker\Exception\CommandNotFoundException;
use ClickNow\Checker\Exception\TaskAlreadyRegisteredException;
use ClickNow\Checker\Exception\TaskNotFoundException;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\OptionsResolver\OptionsResolver;

abstract class AbstractCompilerPass implements CompilerPassInterface
{
    const TAG_TASK = 'checker.task';

    /**
     * @var \Symfony\Component\DependencyInjection\ContainerBuilder
     */
    protected $container;

    /**
     * @var array
     */
    protected static $tasks = [];

    /**
     * Process container builder to run.
     *
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        $this->container = $container;
        $this->run();
    }

    /**
     * Run.
     *
     * @return void
     */
    abstract protected function run();

    /**
     * Checks whether the service exists if not create the same.
     *
     * @param string $serviceId
     * @param string $serviceName
     *
     * @return \Symfony\Component\DependencyInjection\Definition
     */
    protected function registerService($serviceId, $serviceName)
    {
        // Checks if there is already a service with this identifier
        if ($this->container->hasDefinition($serviceId)) {
            return $this->container->findDefinition($serviceId);
        }

        // Register service
        return $this->container->register($serviceId, Command::class)
            ->addArgument(new Reference('checker'))
            ->addArgument($serviceName);
    }

    /**
     * Add tasks in definition.
     *
     * @param \Symfony\Component\DependencyInjection\Definition $definition
     * @param array                                             $tasks
     */
    protected function addTasks(Definition $definition, array $tasks)
    {
        $parsedTasks = $this->parseTasks($tasks);

        foreach ($parsedTasks as $id => $config) {
            $definition->addMethodCall('addAction', [new Reference($id), (array) $config]);
        }
    }

    /**
     * Parse tasks.
     *
     * @param array $tasks
     *
     * @throws \ClickNow\Checker\Exception\TaskNotFoundException
     * @throws \ClickNow\Checker\Exception\TaskAlreadyRegisteredException
     *
     * @return array
     */
    protected function parseTasks(array $tasks)
    {
        $parsed = [];
        $services = $this->getTasksServices();
        $configured = [];

        foreach ($tasks as $name => $config) {
            // Checks if there is a task service with this identifier
            if (!array_key_exists($name, $services)) {
                throw new TaskNotFoundException($name);
            }

            // Checks if the task has already been configured
            if (array_key_exists($name, $configured)) {
                throw new TaskAlreadyRegisteredException($name);
            }

            $parsed[$services[$name]] = $config;
            $configured[$name] = $config;
        }

        return $parsed;
    }

    /**
     * Get tasks services.
     *
     * @return array
     */
    private function getTasksServices()
    {
        if (!empty(self::$tasks)) {
            return self::$tasks;
        }

        $taggedServices = $this->container->findTaggedServiceIds(self::TAG_TASK);

        foreach ($taggedServices as $id => $tags) {
            $taskTags = $this->getTaskTags($tags);
            self::$tasks[$taskTags['config']] = $id;
        }

        return self::$tasks;
    }

    /**
     * Get task tags.
     *
     * @param array $tags
     *
     * @return array
     */
    private function getTaskTags(array $tags)
    {
        $resolver = new OptionsResolver();
        $resolver->setRequired(['config']);

        return $resolver->resolve(current($tags));
    }

    /**
     * Add commands in definition.
     *
     * @param \Symfony\Component\DependencyInjection\Definition $definition
     * @param array                                             $commands
     *
     * @throws \ClickNow\Checker\Exception\CommandNotFoundException
     * @throws \ClickNow\Checker\Exception\CommandAlreadyRegisteredException
     */
    protected function addCommands(Definition $definition, array $commands)
    {
        $registered = [];

        foreach ($commands as $name => $config) {
            $id = 'command.'.$name;

            // Checks if there is a command service with this identifier
            if (!$this->container->hasDefinition($id)) {
                throw new CommandNotFoundException($name);
            }

            // Checks if the command has already been registered
            if (array_key_exists($name, $registered)) {
                throw new CommandAlreadyRegisteredException($name);
            }

            $registered[$name] = $config;
            $definition->addMethodCall('addAction', [new Reference($id), (array) $config]);
        }
    }
}
