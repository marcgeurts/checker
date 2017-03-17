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
            'config'         => null,
            'exclude'        => [],
            'fix'            => false,
            'force'          => false,
            'init'           => false,
            'out'            => null,
            'project'        => null,
            'rules-dir'      => null,
            'formatters-dir' => null,
            'format'         => null,
            'test'           => false,
            'type-check'     => false,
            'finder'         => ['extensions' => ['ts']],
        ]);

        $resolver->addAllowedTypes('config', ['null', 'string']);
        $resolver->addAllowedTypes('exclude', ['array']);
        $resolver->addAllowedTypes('fix', ['bool']);
        $resolver->addAllowedTypes('force', ['bool']);
        $resolver->addAllowedTypes('init', ['bool']);
        $resolver->addAllowedTypes('out', ['null', 'string']);
        $resolver->addAllowedTypes('project', ['null', 'string']);
        $resolver->addAllowedTypes('rules-dir', ['null', 'string']);
        $resolver->addAllowedTypes('formatters-dir', ['null', 'string']);
        $resolver->addAllowedTypes('format', ['null', 'string']);
        $resolver->addAllowedTypes('test', ['bool']);
        $resolver->addAllowedTypes('type-check', ['bool']);

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
        $arguments->addOptionalArgumentWithSeparatedValue('--config', $config['config']);
        $arguments->addArgumentArrayWithSeparatedValue('--exclude', $config['exclude']);
        $arguments->addOptionalArgument('--fix', $config['fix']);
        $arguments->addOptionalArgument('--force', $config['force']);
        $arguments->addOptionalArgument('--init', $config['init']);
        $arguments->addOptionalArgumentWithSeparatedValue('--out', $config['out']);
        $arguments->addOptionalArgumentWithSeparatedValue('--project', $config['project']);
        $arguments->addOptionalArgumentWithSeparatedValue('--rules-dir', $config['rules-dir']);
        $arguments->addOptionalArgumentWithSeparatedValue('--formatters-dir', $config['formatters-dir']);
        $arguments->addOptionalArgumentWithSeparatedValue('--format', $config['format']);
        $arguments->addOptionalArgument('--test', $config['test']);
        $arguments->addOptionalArgument('--type-check', $config['type-check']);
        $arguments->addFiles($files);

        return $arguments;
    }
}
