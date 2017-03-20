<?php

namespace ClickNow\Checker\Task;

use ClickNow\Checker\Repository\FilesCollection;
use ClickNow\Checker\Runner\RunnerInterface;

class SassLint extends AbstractExternalTask
{
    /**
     * Get name.
     *
     * @return string
     */
    public function getName()
    {
        return 'SassLint';
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
            'config'       => null,
            'format'       => null,
            'ignore'       => [],
            'max-warnings' => null,
            'output'       => null,
            'no-exit'      => false,
            'syntax'       => false,
            'verbose'      => $this->io->isVerbose(),
            'finder'       => ['extensions' => ['sass', 'scss']],
        ]);

        $resolver->addAllowedTypes('config', ['null', 'string']);
        $resolver->addAllowedTypes('format', ['null', 'string']);
        $resolver->addAllowedTypes('ignore', ['array']);
        $resolver->addAllowedTypes('max-warnings', ['null', 'int']);
        $resolver->addAllowedTypes('output', ['null', 'string']);
        $resolver->addAllowedTypes('no-exit', ['bool']);
        $resolver->addAllowedTypes('syntax', ['bool']);
        $resolver->addAllowedTypes('verbose', ['bool']);

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
        $arguments = $this->processBuilder->createArgumentsForCommand('sass-lint');
        $arguments->addOptionalArgumentWithSeparatedValue('--config', $config['config']);
        $arguments->addOptionalArgumentWithSeparatedValue('--format', $config['format']);
        $arguments->addOptionalCommaSeparatedArgumentWithSeparatedValue('--ignore', $config['ignore']);
        $arguments->addOptionalArgumentWithSeparatedValue('--max-warnings', $config['max-warnings']);
        $arguments->addOptionalArgumentWithSeparatedValue('--output', $config['output']);
        $arguments->addOptionalArgument('--no-exit', $config['no-exit']);
        $arguments->addOptionalArgument('--syntax', $config['syntax']);
        $arguments->addOptionalArgument('--verbose', $config['verbose']);
        $arguments->addFiles($files);

        return $arguments;
    }
}
