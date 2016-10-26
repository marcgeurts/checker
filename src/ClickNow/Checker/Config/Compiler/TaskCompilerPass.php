<?php

namespace ClickNow\Checker\Config\Compiler;

class TaskCompilerPass extends AbstractCompilerPass
{
    /**
     * Configure tasks.
     *
     * @return void
     */
    protected function configure()
    {
        $tasks = $this->parseTasks((array) $this->container->getParameter('tasks'));

        foreach ($tasks as $task => $config) {
            $this->configureTask($task, (array) $config);
        }
    }

    /**
     * Configure task.
     *
     * @param string $task
     * @param array  $config
     *
     * @return void
     */
    private function configureTask($task, array $config = [])
    {
        // The task definition
        $definition = $this->container->findDefinition($task);

        // Merge default config of the task
        $definition->addMethodCall('mergeDefaultConfig', [$config]);
    }
}
