<?php

namespace ClickNow\Checker\Task;

use ClickNow\Checker\Repository\FilesCollection;
use ClickNow\Checker\Runner\RunnerInterface;

class EsLint extends AbstractExternalTask
{
    /**
     * Get name.
     *
     * @return string
     */
    public function getName()
    {
        return 'ESLint';
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
            'config'           => null,
            'no-eslintrc'      => false,
            'env'              => [],
            'global'           => [],
            'parser'           => null,
            'parser-options'   => [],
            'cache'            => false,
            'cache-location'   => null,
            'rulesdir'         => [],
            'plugin'           => [],
            'rule'             => [],
            'ignore-path'      => null,
            'no-ignore'        => false,
            'ignore-pattern'   => [],
            'stdin'            => false,
            'stdin-filename'   => null,
            'quiet'            => $this->io->isQuiet(),
            'max-warnings'     => null,
            'output-file'      => null,
            'format'           => null,
            'color'            => $this->io->isDecorated(),
            'no-color'         => !$this->io->isDecorated(),
            'init'             => false,
            'fix'              => false,
            'debug'            => $this->io->isDebug(),
            'no-inline-config' => false,
            'print-config'     => false,
            'finder'           => ['extensions' => ['js']],
        ]);

        $resolver->addAllowedTypes('config', ['null', 'string']);
        $resolver->addAllowedTypes('no-eslintrc', ['bool']);
        $resolver->addAllowedTypes('env', ['array']);
        $resolver->addAllowedTypes('global', ['array']);
        $resolver->addAllowedTypes('parser', ['null', 'string']);
        $resolver->addAllowedTypes('parser-options', ['array']);
        $resolver->addAllowedTypes('cache', ['bool']);
        $resolver->addAllowedTypes('cache-location', ['null', 'string']);
        $resolver->addAllowedTypes('rulesdir', ['array']);
        $resolver->addAllowedTypes('plugin', ['array']);
        $resolver->addAllowedTypes('rule', ['array']);
        $resolver->addAllowedTypes('ignore-path', ['null', 'string']);
        $resolver->addAllowedTypes('no-ignore', ['bool']);
        $resolver->addAllowedTypes('ignore-pattern', ['array']);
        $resolver->addAllowedTypes('stdin', ['bool']);
        $resolver->addAllowedTypes('stdin-filename', ['null', 'string']);
        $resolver->addAllowedTypes('quiet', ['bool']);
        $resolver->addAllowedTypes('max-warnings', ['null', 'int']);
        $resolver->addAllowedTypes('output-file', ['null', 'string']);
        $resolver->addAllowedTypes('format', ['null', 'string']);
        $resolver->addAllowedTypes('color', ['bool']);
        $resolver->addAllowedTypes('no-color', ['bool']);
        $resolver->addAllowedTypes('init', ['bool']);
        $resolver->addAllowedTypes('fix', ['bool']);
        $resolver->addAllowedTypes('debug', ['bool']);
        $resolver->addAllowedTypes('no-inline-config', ['bool']);
        $resolver->addAllowedTypes('print-config', ['bool']);

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
        $arguments = $this->processBuilder->createArgumentsForCommand('eslint');
        $arguments->addOptionalArgumentWithSeparatedValue('--config', $config['config']);
        $arguments->addOptionalArgument('--no-eslintrc', $config['no-eslintrc']);
        $arguments->addArgumentArrayWithSeparatedValue('--env', $config['env']);
        $arguments->addArgumentArrayWithSeparatedValue('--global', $config['global']);
        $arguments->addOptionalArgumentWithSeparatedValue('--parser', $config['parser']);
        $arguments->addArgumentArrayWithSeparatedValue('--parser-options', $config['parser-options']);
        $arguments->addOptionalArgument('--cache', $config['cache']);
        $arguments->addOptionalArgumentWithSeparatedValue('--cache-location', $config['cache-location']);
        $arguments->addArgumentArrayWithSeparatedValue('--rulesdir', $config['rulesdir']);
        $arguments->addArgumentArrayWithSeparatedValue('--plugin', $config['plugin']);
        $arguments->addArgumentArrayWithSeparatedValue('--rule', $config['rule']);
        $arguments->addOptionalArgumentWithSeparatedValue('--ignore-path', $config['ignore-path']);
        $arguments->addOptionalArgument('--no-ignore', $config['no-ignore']);
        $arguments->addArgumentArrayWithSeparatedValue('--ignore-pattern', $config['ignore-pattern']);
        $arguments->addOptionalArgument('--stdin', $config['stdin']);
        $arguments->addOptionalArgumentWithSeparatedValue('--stdin-filename', $config['stdin-filename']);
        $arguments->addOptionalArgument('--quiet', $config['quiet']);
        $arguments->addOptionalArgumentWithSeparatedValue('--max-warnings', $config['max-warnings']);
        $arguments->addOptionalArgumentWithSeparatedValue('--output-file', $config['output-file']);
        $arguments->addOptionalArgumentWithSeparatedValue('--format', $config['format']);
        $arguments->addOptionalArgument('--color', $config['color']);
        $arguments->addOptionalArgument('--no-color', $config['no-color']);
        $arguments->addOptionalArgument('--init', $config['init']);
        $arguments->addOptionalArgument('--fix', $config['fix']);
        $arguments->addOptionalArgument('--debug', $config['debug']);
        $arguments->addOptionalArgument('--no-inline-config', $config['no-inline-config']);
        $arguments->addOptionalArgument('--print-config', $config['print-config']);
        $arguments->addFiles($files);

        return $arguments;
    }
}
