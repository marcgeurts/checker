<?php

namespace ClickNow\Checker\Task;

use ClickNow\Checker\Repository\FilesCollection;
use ClickNow\Checker\Runner\RunnerInterface;

class TsLint extends AbstractExternalTask
{
    /**
     * Get name.
     *
     * @return string
     */
    public function getName()
    {
        return 'TSLint';
    }

    /**
     * Get config options.
     *
     * @param \ClickNow\Checker\Runner\RunnerInterface $runner
     *
     * @return \Symfony\Component\OptionsResolver\OptionsResolver
     */
    protected function getConfigOptions(RunnerInterface $runner)
    {
        $resolver = parent::getConfigOptions($runner);

        $resolver->setDefaults([
            'config' => null,
            'exclude' => null,
            'fix' => null,
            'force' => null,
            'init' => null,
            'out' => null,
            'project' => null,
            'rules-dir' => null,
            'formatters-dir' => null,
            'format' => null,
            'test' => null,
            'type-check' => null,
            'finder' => ['extensions' => ['ts']],
        ]);

        $resolver->addAllowedTypes('config', ['null']);
        $resolver->addAllowedTypes('exclude', ['null']);
        $resolver->addAllowedTypes('fix', ['null']);
        $resolver->addAllowedTypes('force', ['null']);
        $resolver->addAllowedTypes('init', ['null']);
        $resolver->addAllowedTypes('out', ['null']);
        $resolver->addAllowedTypes('project', ['null']);
        $resolver->addAllowedTypes('rules-dir', ['null']);
        $resolver->addAllowedTypes('formatters-dir', ['null']);
        $resolver->addAllowedTypes('format', ['null']);
        $resolver->addAllowedTypes('test', ['null']);
        $resolver->addAllowedTypes('type-check', ['null']);

        return $resolver;
    }

    /**
     * Create arguments.
     *
     * @param array                                        $config
     * @param \ClickNow\Checker\Repository\FilesCollection $files
     *
     * @return \ClickNow\Checker\Process\ArgumentsCollection
     */
    protected function createArguments(array $config, FilesCollection $files)
    {
        $arguments = $this->processBuilder->createArgumentsForCommand('tslint');

        return $arguments;
    }
}
