<?php

namespace ClickNow\Checker\Config\Compiler;

use ClickNow\Checker\Exception\CommandInvalidException;

class CommandCompilerPass extends AbstractCompilerPass
{
    /**
     * Configure commands.
     *
     * @return void
     */
    protected function configure()
    {
        $commandsCollection = $this->container->findDefinition('commands_collection');
        $commands = (array) $this->container->getParameter('commands');

        foreach ($commands as $command => $config) {
            $commandsCollection->addMethodCall('set', [$command, $this->addCommand($command, (array) $config)]);
        }

        foreach ($commands as $command => $config) {
            $this->addCommands(
                $this->container->findDefinition('command.'.$command),
                isset($config['commands']) ? (array) $config['commands'] : []
            );
        }
    }

    /**
     * Add command.
     *
     * @param string $command
     * @param array  $config
     *
     * @throws \ClickNow\Checker\Exception\CommandInvalidException
     *
     * @return \Symfony\Component\DependencyInjection\Definition
     */
    private function addCommand($command, array $config = [])
    {
        if (array_key_exists($command, $this->getTasksServices())) {
            throw new CommandInvalidException(
                $command,
                sprintf('The name of a command `%s` can not be the same as the name of a task.', $command)
            );
        }

        $definition = $this->registerCommand('command.'.$command, $command);

        $this->addTasks($definition, isset($config['tasks']) ? (array) $config['tasks'] : []);

        unset($config['tasks'], $config['commands']);
        $definition->addMethodCall('setConfig', [$config]);

        return $definition;
    }
}
