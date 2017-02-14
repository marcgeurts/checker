<?php

namespace ClickNow\Checker\Task;

use ClickNow\Checker\Repository\FilesCollection;
use ClickNow\Checker\Runner\RunnerInterface;

class Atoum extends AbstractExternalTask
{
    /**
     * Get name.
     *
     * @return string
     */
    public function getName()
    {
        return 'Atoum';
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
            'configuration'                   => null,
            'php'                             => null,
            'default-report-title'            => null,
            'score-file'                      => null,
            'max-children-number'             => null,
            'no-code-coverage'                => false,
            'no-code-coverage-in-directories' => [],
            'no-code-coverage-for-namespaces' => [],
            'no-code-coverage-for-classes'    => [],
            'no-code-coverage-for-methods'    => [],
            'enable-branch-and-path-coverage' => false,
            'files'                           => [],
            'directories'                     => [],
            'test-file-extensions' => [],
            'glob'                            => [],
            'tags'                            => [],
            'methods'                         => [],
            'namespaces'                      => [],
            'force-terminal'                  => false,
            'autoloader-file'                 => null,
            'bootstrap-file'                  => null,
            'use-light-report'                => false,
            'use-tap-report'                  => false,
            'debug'                           => $this->io->isDebug(),
            'xdebug-config'                   => false,
            'fail-if-void-methods'            => false,
            'fail-if-skipped-methods'         => false,
            'verbose'                         => $this->io->isVerbose(),
            'finder'                          => ['extensions' => ['php']],
        ]);

        $resolver->addAllowedTypes('configuration', ['null', 'string']);
        $resolver->addAllowedTypes('php', ['null', 'string']);
        $resolver->addAllowedTypes('default-report-title', ['null', 'string']);
        $resolver->addAllowedTypes('score-file', ['null', 'string']);
        $resolver->addAllowedTypes('max-children-number', ['null', 'int']);
        $resolver->addAllowedTypes('no-code-coverage', ['bool']);
        $resolver->addAllowedTypes('no-code-coverage-in-directories', ['array']);
        $resolver->addAllowedTypes('no-code-coverage-for-namespaces', ['array']);
        $resolver->addAllowedTypes('no-code-coverage-for-classes', ['array']);
        $resolver->addAllowedTypes('no-code-coverage-for-methods', ['array']);
        $resolver->addAllowedTypes('enable-branch-and-path-coverage', ['bool']);
        $resolver->addAllowedTypes('files', ['array']);
        $resolver->addAllowedTypes('directories', ['array']);
        $resolver->addAllowedTypes('test-file-extensions', ['array']);
        $resolver->addAllowedTypes('glob', ['array']);
        $resolver->addAllowedTypes('tags', ['array']);
        $resolver->addAllowedTypes('methods', ['array']);
        $resolver->addAllowedTypes('namespaces', ['array']);
        $resolver->addAllowedTypes('force-terminal', ['bool']);
        $resolver->addAllowedTypes('autoloader-file', ['null', 'string']);
        $resolver->addAllowedTypes('bootstrap-file', ['null', 'string']);
        $resolver->addAllowedTypes('use-light-report', ['bool']);
        $resolver->addAllowedTypes('use-tap-report', ['bool']);
        $resolver->addAllowedTypes('debug', ['bool']);
        $resolver->addAllowedTypes('xdebug-config', ['bool']);
        $resolver->addAllowedTypes('fail-if-void-methods', ['bool']);
        $resolver->addAllowedTypes('fail-if-skipped-methods', ['bool']);
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
        $arguments = $this->processBuilder->createArgumentsForCommand('atoum');
        $arguments->addOptionalArgumentWithSeparatedValue('--configuration', $config['configuration']);
        $arguments->addOptionalArgumentWithSeparatedValue('--php', $config['php']);
        $arguments->addOptionalArgumentWithSeparatedValue('--default-report-title', $config['default-report-title']);
        $arguments->addOptionalArgumentWithSeparatedValue('--score-file', $config['score-file']);
        $arguments->addOptionalArgumentWithSeparatedValue('--max-children-number', $config['max-children-number']);
        $arguments->addOptionalArgument('--no-code-coverage', $config['no-code-coverage']);
        $arguments->addSeparatedArgumentArray(
            '--no-code-coverage-in-directories',
            $config['no-code-coverage-in-directories']
        );
        $arguments->addSeparatedArgumentArray(
            '--no-code-coverage-for-namespaces',
            $config['no-code-coverage-for-namespaces']
        );
        $arguments->addSeparatedArgumentArray(
            '--no-code-coverage-for-classes',
            $config['no-code-coverage-for-classes']
        );
        $arguments->addSeparatedArgumentArray(
            '--no-code-coverage-for-methods',
            $config['no-code-coverage-for-methods']
        );
        $arguments->addOptionalArgument(
            '--enable-branch-and-path-coverage',
            $config['enable-branch-and-path-coverage']
        );
        $arguments->addSeparatedArgumentArray('--files', $config['files']);
        $arguments->addSeparatedArgumentArray('--directories', $config['directories']);
        $arguments->addSeparatedArgumentArray('--test-file-extensions', $config['test-file-extensions']);
        $arguments->addSeparatedArgumentArray('--glob', $config['glob']);
        $arguments->addSeparatedArgumentArray('--tags', $config['tags']);
        $arguments->addSeparatedArgumentArray('--methods', $config['methods']);
        $arguments->addSeparatedArgumentArray('--namespaces', $config['namespaces']);
        $arguments->addOptionalArgument('--force-terminal', $config['force-terminal']);
        $arguments->addOptionalArgumentWithSeparatedValue('--autoloader-file', $config['autoloader-file']);
        $arguments->addOptionalArgumentWithSeparatedValue('--bootstrap-file', $config['bootstrap-file']);
        $arguments->addOptionalArgument('--use-light-report', $config['use-light-report']);
        $arguments->addOptionalArgument('--use-tap-report', $config['use-tap-report']);
        $arguments->addOptionalArgument('--debug', $config['debug']);
        $arguments->addOptionalArgument('--xdebug-config', $config['xdebug-config']);
        $arguments->addOptionalArgument('--fail-if-void-methods', $config['fail-if-void-methods']);
        $arguments->addOptionalArgument('--fail-if-skipped-methods', $config['fail-if-skipped-methods']);
        $arguments->addOptionalArgument('--verbose', $config['verbose']);

        return $arguments;
    }
}
