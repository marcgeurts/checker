<?php

namespace ClickNow\Checker\Config\Compiler;

use ClickNow\Checker\Exception\CommandInvalidException;
use ClickNow\Checker\Runner\Runner;
use Symfony\Component\DependencyInjection\Reference;

class CommandCompilerPass extends AbstractCompilerPass
{
    /**
     * Configure.
     *
     * @return void
     */
    protected function configure()
    {
        $commandsCollection = $this->container->findDefinition('runner.commands-collection');
        $commands = (array) $this->container->getParameter('commands');

        foreach ($commands as $command => $config) {
            $commandsCollection->addMethodCall('set', [$command, $this->configureCommand($command, (array) $config)]);
        }

        foreach ($commands as $command => $config) {
            $this->addCommands(
                $this->container->findDefinition('runner.command.'.$command),
                isset($config['commands']) ? (array) $config['commands'] : []
            );
        }
    }

    /**
     * Configure command.
     *
     * @param string $name
     * @param array  $config
     *
     * @throws \ClickNow\Checker\Exception\CommandInvalidException
     *
     * @return \Symfony\Component\DependencyInjection\Definition
     */
    private function configureCommand($name, array $config = [])
    {
        if (array_key_exists($name, $this->getTasksServices())) {
            throw new CommandInvalidException($name, sprintf(
                'The name of a command `%s` can not be the same as the name of a task.',
                $name
            ));
        }

        $definition = $this->container
            ->register(sprintf('runner.command.%s', $name), Runner::class)
            ->addArgument(new Reference('checker'))
            ->addArgument($name);

        $this->addTasks($definition, isset($config['tasks']) ? (array) $config['tasks'] : []);
        $this->setConfig($definition, $config);

        return $definition;
    }
}
