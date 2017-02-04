<?php

namespace ClickNow\Checker\Config\Compiler;

use ClickNow\Checker\Exception\CommandNotFoundException;
use ClickNow\Checker\Exception\TaskNotFoundException;
use ClickNow\Checker\Runner\ConfigRunner;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\OptionsResolver\OptionsResolver;

abstract class AbstractCompilerPass implements CompilerPassInterface
{
    /**
     * @var \Symfony\Component\DependencyInjection\ContainerBuilder
     */
    protected $container;

    /**
     * @var array
     */
    private static $tasks = [];

    /**
     * Process container builder to run.
     *
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     *
     * @return void
     */
    public function process(ContainerBuilder $container)
    {
        $this->container = $container;
        $this->configure();
    }

    /**
     * Configure.
     *
     * @return void
     */
    abstract protected function configure();

    /**
     * Add tasks.
     *
     * @param \Symfony\Component\DependencyInjection\Definition $definition
     * @param array                                             $tasks
     *
     * @return void
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
     *
     * @return array
     */
    protected function parseTasks(array $tasks)
    {
        $services = $this->getTasksServices();
        $parsed = [];

        array_walk($tasks, function ($config, $name) use ($services, &$parsed) {
            if (!array_key_exists($name, $services)) {
                throw new TaskNotFoundException($name);
            }

            $parsed[$services[$name]] = $config;
        });

        return $parsed;
    }

    /**
     * Get tasks services.
     *
     * @return array
     */
    protected function getTasksServices()
    {
        if (!empty(self::$tasks)) {
            return self::$tasks;
        }

        $taggedServices = $this->container->findTaggedServiceIds('checker.task');

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
     * Add commands.
     *
     * @param \Symfony\Component\DependencyInjection\Definition $definition
     * @param array                                             $commands
     *
     * @throws \ClickNow\Checker\Exception\CommandNotFoundException
     *
     * @return void
     */
    protected function addCommands(Definition $definition, array $commands)
    {
        foreach ($commands as $name => $config) {
            $id = 'runner.command.'.$name;

            if (!$this->container->hasDefinition($id)) {
                throw new CommandNotFoundException($name);
            }

            $definition->addMethodCall('addAction', [new Reference($id), (array) $config]);
        }
    }

    /**
     * Set config.
     *
     * @param \Symfony\Component\DependencyInjection\Definition $definition
     * @param array                                             $config
     *
     * @return void
     */
    protected function setConfig(Definition $definition, array $config = [])
    {
        foreach (ConfigRunner::$configs as $key => $method) {
            if (array_key_exists($key, $config)) {
                $definition->addMethodCall($method, [$config[$key]]);
            }
        }
    }
}
