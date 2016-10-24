<?php

namespace ClickNow\Checker\Config\Compiler;

use ClickNow\Checker\Util\Git;

class HookCompilerPass extends AbstractCompilerPass
{
    /**
     * Configure hooks to run.
     */
    public function run()
    {
        $hooks = (array) $this->container->getParameter('hooks');

        foreach (Git::$hooks as $name) {
            $config = isset($hooks[$name]) ? (array) $hooks[$name] : [];

            // The hook service
            $hook = $this->registerService('hook.'.$name, $name);

            // Add tasks
            $this->addTasks($hook, isset($config['tasks']) ? (array) $config['tasks'] : []);

            // Add commands
            $this->addCommands($hook, isset($config['commands']) ? (array) $config['commands'] : []);

            // Set config
            unset($config['tasks'], $config['commands']);

            $hook->addMethodCall('setConfig', [$config]);
        }
    }
}
