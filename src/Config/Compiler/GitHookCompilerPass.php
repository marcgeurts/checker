<?php

namespace ClickNow\Checker\Config\Compiler;

class GitHookCompilerPass extends AbstractCompilerPass
{
    /**
     * Configure.
     *
     * @return void
     */
    protected function configure()
    {
        $hooks = (array) $this->container->getParameter('git-hooks');

        foreach ($hooks as $name => $config) {
            $this->configureGitHook($name, (array) $config);
        }
    }

    /**
     * Configure git hook.
     *
     * @param string $name
     * @param array  $config
     *
     * @return void
     */
    private function configureGitHook($name, array $config = [])
    {
        $definition = $this->container->findDefinition(sprintf('runner.git-hook.%s', $name));

        $this->addTasks($definition, isset($config['tasks']) ? (array) $config['tasks'] : []);
        $this->addCommands($definition, isset($config['commands']) ? (array) $config['commands'] : []);
        $this->setConfig($definition, $config);
    }
}
