<?php

namespace ClickNow\Checker\Config\Compiler;

use ClickNow\Checker\Repository\Git;

class HookCompilerPass extends AbstractCompilerPass
{
    /**
     * Configure hooks.
     *
     * @return void
     */
    protected function configure()
    {
        $hooks = (array) $this->container->getParameter('hooks');

        foreach (Git::$hooks as $hook) {
            $this->addHook($hook, isset($hooks[$hook]) ? (array) $hooks[$hook] : []);
        }
    }

    /**
     * Add hook.
     *
     * @param string $hook
     * @param array  $config
     *
     * @return void
     */
    private function addHook($hook, array $config = [])
    {
        $definition = $this->registerCommand('hook.'.$hook, $hook);

        $this->addTasks($definition, isset($config['tasks']) ? (array) $config['tasks'] : []);
        $this->addCommands($definition, isset($config['commands']) ? (array) $config['commands'] : []);

        unset($config['tasks'], $config['commands']);
        $definition->addMethodCall('setConfig', [$config]);
    }
}
