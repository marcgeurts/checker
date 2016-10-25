<?php

namespace ClickNow\Checker\Config\Compiler;

class TaskCompilerPass extends AbstractCompilerPass
{
    /**
     * Configure tasks to run.
     *
     * @return void
     */
    public function run()
    {
        $tasks = $this->parseTasks((array) $this->container->getParameter('tasks'));

        foreach ($tasks as $id => $config) {
            // The task service
            $task = $this->container->findDefinition($id);

            // Merge default config of the task
            $task->addMethodCall('mergeDefaultConfig', [(array) $config]);
        }
    }
}
