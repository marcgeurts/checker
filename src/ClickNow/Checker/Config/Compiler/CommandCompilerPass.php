<?php

namespace ClickNow\Checker\Config\Compiler;

use ClickNow\Checker\Exception\CommandInvalidException;

class CommandCompilerPass extends AbstractCompilerPass
{
    /**
     * Configure commands to run.
     *
     * @throws \ClickNow\Checker\Exception\CommandInvalidException
     *
     * @return void
     */
    public function run()
    {
        $commandsCollection = $this->container->findDefinition('commands_collection');
        $commands = (array) $this->container->getParameter('commands');

        foreach ($commands as $name => $config) {
            // Check if name of a command is the same as the name of a task
            if (array_key_exists($name, self::$tasks)) {
                throw new CommandInvalidException($name, sprintf(
                    'The name of a command `%s` can not be the same as the name of a task.',
                    $name
                ));
            }

            // The command service
            $command = $this->registerService('command.'.$name, $name);

            // Add tasks
            $this->addTasks($command, isset($config['tasks']) ? (array) $config['tasks'] : []);

            // Set config
            unset($config['tasks'], $config['commands']);

            $command->addMethodCall('setConfig', [(array) $config]);

            // Add command to collection
            $commandsCollection->addMethodCall('set', [$name, $command]);
        }

        // Add commands in commands after all registered
        foreach ($commands as $name => $config) {
            $this->addCommands(
                $this->container->findDefinition('command.'.$name),
                isset($config['commands']) ? (array) $config['commands'] : []
            );
        }
    }
}
