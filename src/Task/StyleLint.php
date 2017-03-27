<?php

namespace ClickNow\Checker\Task;

use ClickNow\Checker\Repository\FilesCollection;
use ClickNow\Checker\Runner\RunnerInterface;

class StyleLint extends AbstractExternalTask
{
    /**
     * Get name.
     *
     * @return string
     */
    public function getName()
    {
        return 'StyleLint';
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
            'config'                   => null,
            'config-basedir'           => null,
            'ignore-path'              => null,
            'syntax'                   => null,
            'custom-syntax'            => null,
            'stdin-filename'           => null,
            'ignore-disables'          => false,
            'cache'                    => false,
            'cache-location'           => null,
            'formatter'                => null,
            'custom-formatter'         => null,
            'quiet'                    => $this->io->isQuiet(),
            'color'                    => $this->io->isDecorated(),
            'no-color'                 => !$this->io->isDecorated(),
            'allow-empty-input'        => false,
            'report-needless-disables' => false,
            'finder'                   => ['extensions' => ['css', 'sass', 'scss', 'less', 'sss']],
        ]);

        $resolver->addAllowedTypes('config', ['null', 'string']);
        $resolver->addAllowedTypes('config-basedir', ['null', 'string']);
        $resolver->addAllowedTypes('ignore-path', ['null', 'string']);
        $resolver->addAllowedTypes('syntax', ['null', 'string']);
        $resolver->addAllowedTypes('custom-syntax', ['null', 'string']);
        $resolver->addAllowedTypes('stdin-filename', ['null', 'string']);
        $resolver->addAllowedTypes('ignore-disables', ['bool']);
        $resolver->addAllowedTypes('cache', ['bool']);
        $resolver->addAllowedTypes('cache-location', ['null', 'string']);
        $resolver->addAllowedTypes('formatter', ['null', 'string']);
        $resolver->addAllowedTypes('custom-formatter', ['null', 'string']);
        $resolver->addAllowedTypes('quiet', ['bool']);
        $resolver->addAllowedTypes('color', ['bool']);
        $resolver->addAllowedTypes('no-color', ['bool']);
        $resolver->addAllowedTypes('allow-empty-input', ['bool']);
        $resolver->addAllowedTypes('report-needless-disables', ['bool']);

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
        $arguments = $this->processBuilder->createArgumentsForCommand('stylelint');
        $arguments->addOptionalArgumentWithSeparatedValue('--config', $config['config']);
        $arguments->addOptionalArgumentWithSeparatedValue('--config-basedir', $config['config-basedir']);
        $arguments->addOptionalArgumentWithSeparatedValue('--ignore-path', $config['ignore-path']);
        $arguments->addOptionalArgumentWithSeparatedValue('--syntax', $config['syntax']);
        $arguments->addOptionalArgumentWithSeparatedValue('--custom-syntax', $config['custom-syntax']);
        $arguments->addOptionalArgumentWithSeparatedValue('--stdin-filename', $config['stdin-filename']);
        $arguments->addOptionalArgument('--ignore-disables', $config['ignore-disables']);
        $arguments->addOptionalArgument('--cache', $config['cache']);
        $arguments->addOptionalArgumentWithSeparatedValue('--cache-location', $config['cache-location']);
        $arguments->addOptionalArgumentWithSeparatedValue('--formatter', $config['formatter']);
        $arguments->addOptionalArgumentWithSeparatedValue('--custom-formatter', $config['custom-formatter']);
        $arguments->addOptionalArgument('--quiet', $config['quiet']);
        $arguments->addOptionalArgument('--color', $config['color']);
        $arguments->addOptionalArgument('--no-color', $config['no-color']);
        $arguments->addOptionalArgument('--allow-empty-input', $config['allow-empty-input']);
        $arguments->addOptionalArgument('--report-needless-disables', $config['report-needless-disables']);
        $arguments->addFiles($files);

        return $arguments;
    }
}
